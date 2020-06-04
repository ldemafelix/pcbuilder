<?php

namespace App\Sources;

use App\Contracts\SourceInterface;
use App\Part;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PCWorx implements SourceInterface
{
    /**
     * The vendor name for this source.
     *
     * @var string
     */
    protected $vendor = 'PCWORX';

    /**
     * Stores the data.
     *
     * @var Response
     */
    protected $data;

    /**
     * The CLI handler.
     *
     * @var
     */
    protected $cli;

    /**
     * The response st atus.
     *
     * @var int
     */
    protected $status;

    /**
     * Map the part types.
     *
     * @var array
     */
    protected $types = [
        'Processor' => 'CPU',
        'Motherboard' => 'Motherboard',
        'Graphics Card' => 'GPU',
        'Memory' => 'RAM',
        'Casing' => 'Case',
        'Power Supply' => 'Power Supply',
        'CPU Cooler' => 'CPU Cooler',
        'SSD' => 'SSD',
        'Desktop HDD' => 'HDD'
    ];

    /**
     * PCWorx constructor.
     *
     * @param Command $cli
     */
    public function __construct(Command $cli)
    {
        $this->cli = $cli;
        try {
            $this->data = Http::withOptions(['verify' => false])->get('https://www.pcworx.ph/getpricelist');
        } catch (\Exception $e) {
            // Do nothing
        } finally {
            $this->status = $this->data->status();
        }
    }

    /**
     * Returns the request status.
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Writes the data to the database.
     */
    public function process()
    {
        // Decode the data
        $results = json_decode($this->data->body());

        // Progress bar
        $bar = $this->cli->getOutput()->createProgressBar(count($results));
        $bar->start();

        // Process
        foreach ($results as $result) {
            // Check if this part is included in the things we need
            if (array_key_exists($result->productSubCategory, $this->types)) {
                Part::updateOrCreate(['vendor' => $this->vendor, 'name' => $result->productName], [
                    'class' => $this->types[$result->productSubCategory],
                    'price' => $result->productSRP
                ]);
            }
            $bar->advance();
        }

        // Finished
        $bar->finish();
    }
}