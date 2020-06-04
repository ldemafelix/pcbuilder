<?php

namespace App\Libraries;

use App\User;
use Illuminate\Support\Facades\Http;

class XenForoHelper
{
    public static function getInstance()
    {
        return Http::withHeaders(['XF-Api-Key' => config('auth.xenforo_key')]);
    }

    public static function authenticate($username, $password)
    {
        $response = self::getInstance()
            ->asForm()
            ->post(
                config('services.xenforo.endpoint') . 'auth',
                [
                    'login' => $username,
                    'password' => $password
                ]
            );
        if ($response->ok()) {
            // Store data in session
            session()->put('xenforo', json_decode($response->body())->user);

            // Add to database
            User::updateOrCreate(
                [
                    'xenforo_id' => session()->get('xenforo')->user_id
                ]
            );
            return true;
        }
        return false;
    }

    public static function getUserDetails($userId)
    {
        // Get the xenForo ID
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Get data
        $response = self::getInstance()
            ->get(config('services.xenforo.endpoint') . 'users/' . $user->xenforo_id);

        // Return
        if ($response->ok()) {
            return json_decode($response->body());
        }
        return false;
    }
}