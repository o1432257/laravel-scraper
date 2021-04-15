<?php

namespace App\Console\Commands;

use App\Models\Telegram;
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
    private $date;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'NBA:scraper {date?}';

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
        $this->setDate();
        $this->setUrl();
        $this->getNBA();
        $this->setNotice();

        Notification::route(TelegramChannel::class, '')
            ->notify(new InvoicePaid($this->notice));
    }

    public function setUrl()
    {
        $this->url .= $this->date;
    }

    public function getNBA()
    {
        $client = new Client();

        $page = $client->request('GET', $this->url);
        $data = json_decode($page->getBody()->getContents());
        foreach ($data->payload->date->games as $key => $value) {
            $this->results[$key] = $value->awayTeam->profile->name . " VS " . $value->homeTeam->profile->name .
                " " . $value->boxscore->awayScore . ":" . $value->boxscore->homeScore . $this->matchStatus[$value->boxscore->status - 1];
        }
    }

    public function setNotice()
    {
        $this->notice = $this->date . "\n";

        foreach ($this->results as $key => $value) {
            $this->notice .= $value . "\n";
        }
    }

    public function setDate()
    {
        if (date('Y-m-d', strtotime($this->argument('date'))) == $this->argument('date')) {
            $this->date = $this->argument('date');
        } else {
            $this->date = Carbon::now()->toDateString();
        }
    }

}
