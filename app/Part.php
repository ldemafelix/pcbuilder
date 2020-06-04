<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = [
        'vendor', 'name', 'class', 'price'
    ];
}
