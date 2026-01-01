<?php

namespace App\Console\Commands;

use App\Http\Controllers\SuuTraController;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class LoadUchiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:store';

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
        $data->store();
    }

}
