<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Build extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'cpu_id', 'gpu_id', 'motherboard_id', 'memory_id', 'memory_quantity', 'casing_id', 'power_supply_id', 'cpu_cooler_id', 'ssd_id', 'hdd_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cpu()
    {
        return $this->hasOne(Part::class, 'id', 'cpu_id');
    }

    public function gpu()
    {
        return $this->hasOne(Part::class, 'id', 'gpu_id');
    }

    public function motherboard()
    {
        return $this->hasOne(Part::class, 'id', 'motherboard_id');
    }

    public function memory()
    {
        return $this->hasOne(Part::class, 'id', 'memory_id');
    }

    public function casing()
    {
        return $this->hasOne(Part::class, 'id', 'casing_id');
    }

    public function power_supply()
    {
        return $this->hasOne(Part::class, 'id', 'power_supply_id');
    }

    public function cpu_cooler()
    {
        return $this->hasOne(Part::class, 'id', 'cpu_cooler_id');
    }

    public function ssd()
    {
        return $this->hasOne(Part::class, 'id', 'ssd_id');
    }

    public function hdd()
    {
        return $this->hasOne(Part::class, 'id', 'hdd_id');
    }
}
