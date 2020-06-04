<?php

namespace App\Contracts;

use Illuminate\Console\Command;

interface SourceInterface
{
    public function __construct(Command $cli);
    public function status();
}