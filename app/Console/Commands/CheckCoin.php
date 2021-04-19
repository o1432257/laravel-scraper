<?php

namespace App\Console\Commands;

use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class checkCoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:coin';

    private $watchMoreUrl = 'https://crypto.cnyes.com/BTC/24h';
    private $results = array();
    //數字為目標金額 達到目標時機器人發出通知
    private $coins = ['BTC' => 60000, 'ETH' => 2400];
    private $notice;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check coin now';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function handle()
    {
        $this->getCoin();

        $this->checkCoin();
    }

    private function checkCoin()
    {
        foreach ($this->coins as $key => $value) {

            if (str_replace( ',', '', $this->results[$key] )  >= $value){

                $this->setNotice($key);

                Notification::route(TelegramChannel::class, '')
                    ->notify(new InvoicePaid($this->notice));
            }
        }
    }

    private function getCoin()
    {
        $client = new Client();

        foreach ($this->coins as $key => $value) {
            $url = 'https://crypto.cnyes.com/' . $key;
            $page = $client->request('GET', $url);
            $this->results[$key] = $page->filter('.last-price')->text();
        }
    }



    private function setNotice($key)
    {
        $this->notice = Carbon::now() . "\n" . "*重返榮耀* \n";

        $this->notice = $this->notice . $key . " : " . $this->results[$key] . " USD" . "\n";

        $this->notice = [
            'message' => $this->notice,
            'watchMoreUrl' => $this->watchMoreUrl
        ];
    }
}
