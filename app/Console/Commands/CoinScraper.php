<?php

namespace App\Console\Commands;

use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class CoinScraper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin:scraper';

    private $results = array();
    private $coins = ['BTC', 'ETH', 'XRP', 'BNB', 'DOGE', 'TRX', 'ADA', 'TLM', 'CHZ', 'XLM'];
    private $notice;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Electronic money';

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
        $this->getCoin();

        $this->setNotice();

        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($this->notice));
    }

    private function getCoin()
    {
        $client = new Client();

        foreach ($this->coins as $coin) {
            $url = 'https://crypto.cnyes.com/' . $coin;
            $page = $client->request('GET', $url);
            $this->results[$coin] = $page->filter('.last-price')->text();
        }
    }

    private function setNotice()
    {
        $this->notice = Carbon::now() . "\n" . "*目前價格* \n";

        foreach ($this->results as $key => $value) {
            $this->notice = $this->notice . $key . " : " . $value . " USD" . "\n";
        }
    }
}
