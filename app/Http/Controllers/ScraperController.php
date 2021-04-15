<?php

namespace App\Http\Controllers;

use App\Models\Telegram;
use App\Notifications\InvoicePaid;
use Carbon\Carbon;

//use Goutte\Client;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class ScraperController extends Controller
{
    private $results = array();
    private $coins = ['BTC', 'ETH', 'XRP', 'BNB', 'DOGE', 'TRX', 'ADA', 'TLM', 'CHZ', 'XLM'];
    private $matchStatus = ['  未開始', '  比賽中', '  比賽結束'];
//
//    public function scraper3070()
//    {
//        $url = 'https://shopee.tw/api/v4/search/search_items?by=relevancy&keyword=rtx%203070&limit=100&order=desc&page_type=search&version=2&price_min=35000&newest=0';
//
//        $headers = [
//                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36',
//                'x-api-source' => 'pc',
//                'Referer' => 'https%3A%2F%2Fshopee.tw%2Fsearch%3Fkeyword%3Drtx%25203070'
//            ];
//        $client = new \GuzzleHttp\Client();
//        $response = $client->request('GET', $url, $headers);
//
//        $data = json_decode($response->getBody()->getContents());
//
//        foreach ($data->items as $item)
//        {
//            $this->results[$item->item_basic->name] = $item->item_basic->price /100000;
//        }
//        dd($this->results);
//    }
    public function scraperNBA()
    {
        $url = 'https://tw.global.nba.com/stats2/scores/daily.json?countryCode=TW&locale=zh_TW&tz=%2B8&gameDate=' . Carbon::now()->toDateString();
        $client = new Client();
        $page = $client->request('GET', $url);
        $data = json_decode($page->getBody()->getContents());
        $text = Carbon::now()->toDateString() . "\n";

        foreach ($data->payload->date->games as $key => $value) {
//            $this->results[$key] = $value->homeTeam->profile->name . " VS " . $value->awayTeam->profile->name .
//                " " . $value->boxscore->awayScore . ":" . $value->boxscore->homeScore . $this->matchStatus[$value->boxscore->status - 1];
            $text = $text . $value->homeTeam->profile->name . " VS " . $value->awayTeam->profile->name .
                " " . $value->boxscore->awayScore . ":" . $value->boxscore->homeScore . $this->matchStatus[$value->boxscore->status - 1] . "\n";
        }
        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($text));
    }

    public function telegram(Request $request)
    {
        dd($request);
        $data = $request->all()['message']['text'];

        if ($data == '籃球')
        {

            $this->scraperNBA();
        }
//        if ($data == '幣價')
//        {
//            $this->coinController->scraperCoin();
//        }
    }

    public function testTelegram()
    {
        Artisan::call('coin:now');
    }

}
