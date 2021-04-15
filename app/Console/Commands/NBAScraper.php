<?php

namespace App\Console\Commands;

use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class NBAScraper extends Command
{
    protected $url = 'https://tw.global.nba.com/stats2/scores/daily.json?countryCode=TW&locale=zh_TW&tz=%2B8&gameDate=';
    protected $results = array();
    private $matchStatus = ['  未開始', '  比賽中', '  比賽結束'];
    private $notice;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'NBA:scraper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'NBA scraper';

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
        $this->setUrl();
        $this->getNBA();
        $this->setNotice();

        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($this->notice));
    }

    public function scraperNBA()
    {
        $url = 'https://tw.global.nba.com/stats2/scores/daily.json?countryCode=TW&locale=zh_TW&tz=%2B8&gameDate=' . Carbon::now()->toDateString();
        $client = new Client();
        $page = $client->request('GET', $url);
        $data = json_decode($page->getBody()->getContents());
        $text = Carbon::now()->toDateString() . "\n";

        foreach ($data->payload->date->games as $key => $value) {
            $text = $text . $value->homeTeam->profile->name . " VS " . $value->awayTeam->profile->name .
                " " . $value->boxscore->awayScore . ":" . $value->boxscore->homeScore . $this->matchStatus[$value->boxscore->status - 1] . "\n";
        }
        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($text));
    }

    public function setUrl()
    {
        $this->url .= Carbon::now()->toDateString();
    }

    public function getNBA()
    {
        $client = new Client();

        $page = $client->request('GET', $this->url);
        $data = json_decode($page->getBody()->getContents());

        foreach ($data->payload->date->games as $key => $value) {

            $this->results[$key] = $value->homeTeam->profile->name . " VS " . $value->awayTeam->profile->name .
                " " . $value->boxscore->awayScore . ":" . $value->boxscore->homeScore . $this->matchStatus[$value->boxscore->status - 1];
        }
    }

    public function setNotice()
    {
        $this->notice = Carbon::now()->toDateString() . "\n";

        foreach ($this->results as $key => $value) {
            $this->notice .= $value . "\n";
        }
    }


}
