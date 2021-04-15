<?php

namespace App\Console\Commands;

use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin:now';

    private $results = array();
    private $coins = ['BTC', 'ETH', 'XRP', 'BNB', 'DOGE', 'TRX', 'ADA', 'TLM', 'CHZ', 'XLM'];
    private $notiable;

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

        $this->setNotiable();

        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($this->notiable));
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

    private function setNotiable()
    {
        $this->notiable = Carbon::now() . "\n" . "*目前價格* \n";

        foreach ($this->results as $key => $value) {
            $this->notiable = $this->notiable . $key . " : " . $value . " USD" . "\n";
        }
    }
}
