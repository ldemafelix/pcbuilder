<?php

namespace App\Sources;

use App\Contracts\SourceInterface;
use App\Part;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PCHub implements SourceInterface
{
    /**
     * The vendor name for this source.
     *
     * @var string
     */
    protected $vendor = 'PCHub';

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
     * The response status.
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
        'GPU' => 'GPU',
        'RAM' => 'RAM',
        'Case' => 'Case',
        'PSU' => 'Power Supply',
        'Cooling' => 'CPU Cooler'
    ];

    /**
     * PCHub constructor.
     *
     * @param Command $cli
     */
    public function __construct(Command $cli)
    {
        $this->cli = $cli;
        try {
            $this->data = Http::withOptions(['verify' => false])->get('https://docs.google.com/spreadsheets/d/10bTlhxgBCCsFCD3wvGMvHzDqKOOFqR-iFpiJFtTHirA/gviz/tq?headers=2&range=A6%3AR&sheet=PLs&tqx=reqId%3A0;responseHandler%3A__JsonpRequest_cb__.getResponse0_');
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
        $results = json_decode(rtrim(str_replace("/*O_o*/\n__JsonpRequest_cb__.getResponse0_(", "", $this->data->body()), ");"))->table->rows;

        // Progress bar
        $bar = $this->cli->getOutput()->createProgressBar(count($results));
        $bar->start();

        // Process
        foreach ($results as $result) {
            // Check if this part is included in the things we need
            if (array_key_exists($result->c[1]->v, $this->types)) {
                Part::updateOrCreate(['vendor' => $this->vendor, 'name' => $result->c[5]->v], [
                    'class' => $this->types[$result->c[1]->v],
                    'price' => $result->c[7]->v
                ]);
            }
            $bar->advance();
        }

        // Finished
        $bar->finish();
    }
}