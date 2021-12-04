<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;


class volume30Kucoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'volume_30_kucoin';

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
    // public function getVolume30_old($kucoin, $symbol)
    // {
    //     $timestamp = $kucoin->seconds();
    //     $now = Carbon::createFromTimestamp($timestamp)->timestamp;
    //     $nowSub24 = Carbon::createFromTimestamp($timestamp)->subMonth(1)->timestamp;

    //     dd([$now, $nowSub24]);


    //     $data = $kucoin->fetch_ohlcv($symbol, '1d', $nowSub24, $now);
    //     dd($data);
    // }

    public function getVolume30($kucoin, $symbol)
    {

        $client = new \GuzzleHttp\Client();

        $timestamp = $kucoin->seconds();
        $now = Carbon::createFromTimestamp($timestamp)->timestamp;
        $nowSub24 = Carbon::createFromTimestamp($timestamp)->subMonth(1)->timestamp;


        //https: //kucoin.com/api/v1/market/candles?type=1min&symbol=BTC-USDT&startAt=1566703297&endAt=1566789757
        $url = "https://api.kucoin.com/api/v1/market/candles";

        try {
            $response = $client->request('GET', $url, [
                'query' => [
                    'symbol' => $symbol,
                    'type' => '1day',
                    'startAt' => $nowSub24,
                    'endAt' =>   $now
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (ClientException $e) {
            return null;
        }

        $candles = json_decode($response->getBody()->getContents());
        if (!$candles) {
            return null;
        }

        $volSum = 0;
        foreach ($candles->data as $candle) {
            $volSum += $candle[5];
        }

        return $volSum / 24;
    }


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
        $kucoin = new \ccxt\kucoin();
        $tickers = Ticker::whereHas('symbol', function ($query) {
            $query->where('exchanger', 'kucoin');
        })->with('symbol')->get();

        foreach ($tickers as  $ticker) {

            if ($ticker->volume_30d > 0) {
                dump($ticker->volume_30d / $ticker->volume_24h);
            }


            // $symbol = $ticker->symbol->symbol;

            // $volume_30d = $this->getVolume30($kucoin, $symbol, now()->timestamp);
            // dump($volume_30d);

            // $ticker->volume_30d = $volume_30d;
            // $ticker->save();
            // sleep(15);
        }

        return Command::SUCCESS;
    }
}
