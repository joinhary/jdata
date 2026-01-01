<?php

namespace App\Console\Commands;
use App\Http\Controllers\SolariumController;
use Illuminate\Console\Command;

class SorlCheck extends Command
{
    protected $client;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:solrcheck';

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
  
    public function __construct(\Solarium\Client $client)
    {
        parent::__construct();
        $this->client = $client;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pingSolr = $this->checkSolr();
        if($pingSolr){
            $data = new SolariumController($this->client);
            $data->check_solr();
        }else{
            // $this->sendMessageToTelegram('[🆘]_[STP2(240)]_[SOLARIUM] Không kết nối với Solr được!');
        }
    
    }
    public function checkSolr()
    {
        $ch = curl_init('http://localhost:8983/solr');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 0) {
            return false;
        }
        return true;
    }
    public function sendMessageToTelegram($text)
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage?chat_id=" . env('TELEGRAM_CHAT_ID') . "&text=" . $text;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
     
    }
}
