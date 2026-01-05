<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SuuTraController;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $data = new SuuTraController();
        $data->import();
    }

}
