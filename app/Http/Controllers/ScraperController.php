<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCoin;
use App\Jobs\ProcessNBA;
use App\Models\Telegram;
use App\Notifications\InvoicePaid;
use Carbon\Carbon;

//use Goutte\Client;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class ScraperController extends Controller
{
    public function telegram(Request $request)
    {
        $data = $request->all()['message']['text'];

        switch ($data)
        {
            case '籃球':
                ProcessNBA::dispatch();
                break;

            case '幣價':
                ProcessCoin::dispatch();
                break;

            default:
                break;
        }
    }
}
