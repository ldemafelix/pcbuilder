<?php

namespace App\Http\Controllers;

use App\Build;
use App\Libraries\ResponseHelper;
use App\Libraries\XenForoHelper;
use App\Part;
use App\User;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function home()
    {
        return view('welcome');
    }

    public function getParts(Request $request)
    {
        // Retrieve the part type
        if (empty($request->get('type'))) {
            return ResponseHelper::send(400, 'No part type specified.');
        }

        // Get a list of valid types
        $type = $request->get('type');
        if (Part::where('class', $type)->count() === 0) {
            return ResponseHelper::send(400, 'Invalid part type.');
        }

        // Prepare the parts
        $parts = Part::where('class', $type);

        // Do we have a search query?
        if (!empty($request->search)) {
            $parts->where('name', 'LIKE', "%{$request->search}%");
        }
        $parts = $parts->paginate(10);
        $total = $parts->total();

        // Reprocess parts
        $processedParts = [];
        foreach ($parts as $part) {
            $processedParts[] = [
                'id' => $part->id,
                'text' => $part->vendor . ' - ' . $part->name . ' (₱' . number_format($part->price, 2) . ')',
                'price' => $part->price,
                'with_multiplier' => $part->with_multiplier
            ];
        }

        // Return a list of parts
        return ResponseHelper::send(200, $total, $processedParts);
    }

    public function createBuild(Request $request)
    {
        if (!session()->has('xenforo')) {
            return ResponseHelper::send(400, 'You are not logged in.');
        }

        $xfBridge = User::where('xenforo_id', session()->get('xenforo')->user_id)->first();
        if (!$xfBridge) {
            return ResponseHelper::send(400, 'Your forum account needs to be refreshed. Please reload this page and sign in again.');
        }

        // Validate
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', Rule::unique('builds', 'name')->where('user_id', $xfBridge->id)],
                'cpu_id' => ['required', 'exists:parts,id'],
                'gpu_id' => ['nullable', 'exists:parts,id'],
                'motherboard_id' => ['required', 'exists:parts,id'],
                'memory_id' => ['required', 'exists:parts,id'],
                'memory_quantity' => ['required', 'numeric', 'min:1'],
                'casing_id' => ['required', 'exists:parts,id'],
                'power_supply_id' => ['nullable', 'exists:parts,id'],
                'cpu_cooler_id' => ['nullable', 'exists:parts,id'],
                'ssd_id' => ['nullable', 'exists:parts,id'],
                'hdd_id' => ['nullable', 'exists:parts,id'],
            ],
            [
                'name.unique' => 'You already have a build with this name. Please use a different name.',
                'name.*' => 'Please enter a valid name for this build.',
                'cpu_id.*' => 'Please choose a valid processor.',
                'gpu_id.*' => 'Please choose a valid graphics card.',
                'motherboard_id.*' => 'Please choose a valid motherboard.',
                'memory_id.*' => 'Please choose a valid memory stick.',
                'memory_quantity.*' => 'Your memory quantity must be greater than or equal to 1.',
                'casing_id.*' => 'Please choose a valid casing.',
                'power_supply_id.*' => 'Please choose a valid power supply.',
                'cpu_cooler_id.*' => 'Please choose a valid CPU Cooler.',
                'ssd_id.*' => 'Please choose a valid SSD.',
                'hdd_id.*' => 'Please choose a valid HDD.',
            ]
        );
        if ($validator->fails()) {
            return ResponseHelper::send(422, $validator->errors()->first(), $validator->errors()->all());
        }

        // Write the build to the database
        $data = collect(
            $request->only(
                'name',
                'cpu_id',
                'gpu_id',
                'motherboard_id',
                'memory_id',
                'memory_quantity',
                'casing_id',
                'power_supply_id',
                'cpu_cooler_id',
                'ssd_id',
                'hdd_id'
            )
        );
        $data->put('user_id', $xfBridge->id);
        $build = Build::create($data->toArray());
        $id = $build->id;
        $build = collect($build);
        $build->put('hash', Hashids::encode($id));

        // Build created.
        return ResponseHelper::send(201, 'Build saved successfully.', $build->toArray());
    }

    public function viewBuild($hash)
    {
        $id = Hashids::decode($hash);

        $build = Build::with(
            'user',
            'cpu',
            'gpu',
            'motherboard',
            'memory',
            'casing',
            'power_supply',
            'cpu_cooler',
            'ssd',
            'hdd'
        )->where('id', $id[0])->first();
        if (!$build) {
            abort(404);
        }

        $xfBridge = User::find($build->user_id);
        if (!$xfBridge) {
            abort(404); // Users who deleted their accounts shouldn't have their builds public
        }

        // Am I allowed to edit?
        $canEdit = false;
        if (session()->has('xenforo') && $xfBridge->xenforo_id == session()->get('xenforo')->user_id) {
            $canEdit = true;
        }

        // Get user info
        $xfInfo = XenForoHelper::getUserDetails($build->user_id);
        if (!$xfInfo) {
            abort(404); // Users who deleted their accounts shouldn't have their builds public
        }

        return view('view-build', compact('build', 'canEdit', 'xfInfo'));
    }

    public function updateBuild(Request $request, $hash)
    {
        $id = Hashids::decode($hash);
        $build = Build::with(
            'user',
            'cpu',
            'gpu',
            'motherboard',
            'memory',
            'casing',
            'power_supply',
            'cpu_cooler',
            'ssd',
            'hdd'
        )->where('id', $id[0])->first();
        if (!$build) {
            abort(404);
        }

        $xfBridge = User::find($build->user_id);
        if (!$xfBridge) {
            return ResponseHelper::send(404);
        }

        // Do we own this build?
        if ($xfBridge->xenforo_id != session()->get('xenforo')->user_id) {
            return ResponseHelper::send(403, 'You do not have privileges to edit this build.');
        }

        // Validate
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', Rule::unique('builds', 'name')->ignore($id[0])->where('user_id', $xfBridge->id)],
                'cpu_id' => ['required', 'exists:parts,id'],
                'gpu_id' => ['nullable', 'exists:parts,id'],
                'motherboard_id' => ['required', 'exists:parts,id'],
                'memory_id' => ['required', 'exists:parts,id'],
                'memory_quantity' => ['required', 'numeric', 'min:1'],
                'casing_id' => ['required', 'exists:parts,id'],
                'power_supply_id' => ['nullable', 'exists:parts,id'],
                'cpu_cooler_id' => ['nullable', 'exists:parts,id'],
                'ssd_id' => ['nullable', 'exists:parts,id'],
                'hdd_id' => ['nullable', 'exists:parts,id'],
            ],
            [
                'name.unique' => 'You already have a build with this name. Please use a different name.',
                'name.*' => 'Please enter a valid name for this build.',
                'cpu_id.*' => 'Please choose a valid processor.',
                'gpu_id.*' => 'Please choose a valid graphics card.',
                'motherboard_id.*' => 'Please choose a valid motherboard.',
                'memory_id.*' => 'Please choose a valid memory stick.',
                'memory_quantity.*' => 'Your memory quantity must be greater than or equal to 1.',
                'casing_id.*' => 'Please choose a valid casing.',
                'power_supply_id.*' => 'Please choose a valid power supply.',
                'cpu_cooler_id.*' => 'Please choose a valid CPU Cooler.',
                'ssd_id.*' => 'Please choose a valid SSD.',
                'hdd_id.*' => 'Please choose a valid HDD.',
            ]
        );
        if ($validator->fails()) {
            return ResponseHelper::send(422, $validator->errors()->first(), $validator->errors()->all());
        }

        // Update
        $build = Build::where('id', $id[0])
            ->update($request->only(
                'name',
                'cpu_id',
                'gpu_id',
                'motherboard_id',
                'memory_id',
                'memory_quantity',
                'casing_id',
                'power_supply_id',
                'cpu_cooler_id',
                'ssd_id',
                'hdd_id'
            ));

        // Return
        return ResponseHelper::send(200, 'Your build has been updated.', ['hash' => $hash]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return ResponseHelper::send(422, 'Please provide a valid username and password.');
        }
        $attempt = \App\Libraries\XenForoHelper::authenticate($request->input('username'), $request->input('password'));
        if ($attempt) {
            return ResponseHelper::send(200, 'Login successful.');
        }
        return ResponseHelper::send(403, 'Invalid credentials.');
    }

    public function deleteBuild($hash)
    {
        $id = Hashids::decode($hash);

        $build = Build::with(
            'user',
            'cpu',
            'gpu',
            'motherboard',
            'memory',
            'casing',
            'power_supply',
            'cpu_cooler',
            'ssd',
            'hdd'
        )->where('id', $id[0])->first();
        if (!$build) {
            abort(404);
        }

        $xfBridge = User::find($build->user_id);
        if (!$xfBridge) {
            abort(404); // Users who deleted their accounts shouldn't have their builds public
        }

        // Am I allowed to edit?
        $canEdit = false;
        if (session()->has('xenforo') && $xfBridge->xenforo_id == session()->get('xenforo')->user_id) {
            $canEdit = true;
        }

        // Delete this build
        $build->delete();

        // We're done
        session()->flash('success', 'Your build has been deleted.');
        return redirect()->route('home');
    }
}
