<?php

namespace App\Http\Controllers;

use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class CoinController extends Controller
{

    private $results = array();
    private $coins = ['BTC', 'ETH', 'XRP', 'BNB', 'DOGE', 'TRX', 'ADA', 'TLM', 'CHZ', 'XLM'];

    public function scraperCoin()
    {
        $client = new Client();

        foreach ($this->coins as $coin) {
            $url = 'https://crypto.cnyes.com/' . $coin;
            $page = $client->request('GET', $url);
            $this->results[$coin] = $page->filter('.last-price')->text();
        }

        $data = $this->results;

        $notiable = Carbon::now() . "*目前價格* \n";
        foreach ($data as $key => $value) {
            $notiable = $notiable . $key . " : " . $value . " USD" . "\n";
        }
        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($notiable));

//        return view('scraper', compact('data'));
    }
}
