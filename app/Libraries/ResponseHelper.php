<?php

namespace App\Libraries;

use App\Build;
use App\Part;

class ResponseHelper
{
    public static function send($status = 200, $message = null, $data = [])
    {
        return response()->json(
            [
                'message' => $message,
                'data' => $data
            ],
            $status
        );
    }

    public static function lastPricingUpdate()
    {
        return Part::orderBy('updated_at', 'DESC')->first();
    }

    public static function getBuildTotal($id)
    {
        // Find the build
        $build = Build::with('cpu', 'gpu', 'motherboard', 'memory', 'casing', 'power_supply', 'cpu_cooler', 'ssd', 'hdd')->where('id', $id)->first();

        // Get prices
        $cpu = $build->cpu->price;
        $gpu = $build->gpu ? $build->gpu->price : 0;
        $motherboard = $build->motherboard ? $build->motherboard->price : 0;
        $memory = ($build->memory ? $build->memory->price : 0) * $build->memory_quantity;
        $casing = $build->casing ? $build->casing->price : 0;
        $powerSupply = $build->power_supply ? $build->power_supply->price : 0;
        $cpuCooler = $build->cpu_cooler ? $build->cpu_cooler->price : 0;
        $ssd = $build->ssd ? $build->ssd->price : 0;
        $hdd = $build->hdd ? $build->hdd->price : 0;

        return $cpu + $gpu + $motherboard + $memory + $casing + $powerSupply + $cpuCooler + $ssd + $hdd;
    }
}