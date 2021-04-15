<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCoin;
use App\Jobs\ProcessNBA;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    private $data;
    private $explodedData = array();

    public function telegram(Request $request)
    {
        if (isset($request->all()['message']['text'])){

            $this->data = $request->all()['message']['text'];

            $this->explodedData = explode(" ",$this->data);

            $this->isExploded();
        }
    }

    private function switch()
    {
        switch ($this->explodedData[0])
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

    private function switchWithData()
    {
        switch ($this->explodedData[0])
        {
            case '籃球':
                ProcessNBA::dispatch($this->explodedData[1]);
                break;

            case '幣價':
                ProcessCoin::dispatch();
                break;

            default:
                break;
        }
    }

    private function isExploded()
    {
        if (isset($this->explodedData[1])){
            $this->switchWithData();
        }else{
            $this->switch();
        }
    }
}
