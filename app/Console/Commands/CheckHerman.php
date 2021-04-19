<?php

namespace App\Console\Commands;

use App\Notifications\InvoicePaid;
use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use Symfony\Component\DomCrawler\Crawler;

class CheckHerman extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:herman';

    private $count = 0;
    private $notice = array();
    private const _LOW_PRICE = 10000;
    private const _HIGH_PRICE = 30000;

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
        $client = new Client();
        $url = 'https://feebee.com.tw/s/?q=herman+miller+aeron&ptab=1&sort=p&mode=l&best=&pl=&ph=&e=&pr%5B%5D=24hpchome';
        $page = $client->request('GET', $url);

        $page->filter('.items')->reduce(function (Crawler $node, $i) {
            $price = $node->filter('.price-info')->text();
            if (self::_LOW_PRICE <= str_replace( ',', '', $price ) & str_replace( ',', '', $price ) <=  self::_HIGH_PRICE){

                $this->notice = [
                    'message' => $node->filter('.large')->text() . "\n" . $price,
                    'watchMoreUrl' => $node->filter('.items_container > .items_link')->link()->getUri()
                ];

                Notification::route(TelegramChannel::class, '')
                    ->notify(new InvoicePaid($this->notice));
            }
        });
    }




}
