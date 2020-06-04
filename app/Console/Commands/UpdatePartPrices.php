<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePartPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parts:update {provider?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the parts price list.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the list of providers
        if (!empty($this->argument('provider'))) {
            $providers = [base_path('app/Sources/' . $this->argument('provider') . '.php')];
        } else {
            $providers = glob(base_path('app/Sources/*.php'));
        }
        foreach ($providers as $provider) {
            $name = str_replace('.php', '', basename($provider));
            $this->getOutput()->write("Connecting to {$name}...");
            $class = "App\Sources\\{$name}";
            $instance = new $class($this);
            if ($instance->status() !== 200) {
                $this->getOutput()->write(" failed. Skipping...");
                continue;
            }
            $this->getOutput()->write(" successful!");
            $this->getOutput()->writeln('');
            $this->line('Writing changes...');

            // Start processing
            $instance->process();

            // New lines
            $this->getOutput()->writeln('');
            $this->getOutput()->writeln('');
        }
    }
}
