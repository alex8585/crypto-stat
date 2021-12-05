<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use App\Models\CoinVolume;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\ClientException;


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
    protected $shouldUpdateArr = [];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function getVolume30_old($kucoin, $symbol)
    {

        $timestamp = $kucoin->seconds();

        // $now = Carbon::createFromTimestamp($timestamp)->timestamp;
        $nowSub24 = Carbon::createFromTimestamp($timestamp)->subMonth(1)->timestamp;

        $candles = $kucoin->fetch_ohlcv($symbol, '1w', $nowSub24 * 1000);
        $volSum = 0;
        foreach ($candles as $candle) {
            $volSum += $candle[5];
        }

        return $volSum;
    }

    public function getVolume30($kucoin, $symbol)
    {

        $client = new \GuzzleHttp\Client();

        $timestamp = $kucoin->seconds();
        $now = Carbon::createFromTimestamp($timestamp)->timestamp;
        $nowSub24 = Carbon::createFromTimestamp($timestamp)->subDays(30)->timestamp;


        //https: //kucoin.com/api/v1/market/candles?type=1min&symbol=BTC-USDT&startAt=1566703297&endAt=1566789757
        $url = "https://api.kucoin.com/api/v1/market/candles";

        try {
            $response = $client->request('GET', $url, [
                'query' => [
                    'symbol' => $symbol,
                    'type' => '1week',
                    'startAt' => $nowSub24,
                    'endAt' =>   $now
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (ClientException $e) {
            dump($e->getMessage());
            return 0;
        }

        $candles = json_decode($response->getBody()->getContents());
        if (!$candles) {
            return 0;
        }

        $volSum = 0;
        foreach ($candles->data as $candle) {
            $volSum += $candle[5];
        }

        return $volSum;
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
        $cnt = 0;
        foreach ($tickers as  $ticker) {

            $volume = CoinVolume::firstOrNew(['ticker_id' =>  $ticker->id]);

            if ($volume->exists) {
                $this->shouldUpdateArr[] = $ticker;
                continue;
            }

            $volume_30d = $this->updateVolume($kucoin, $ticker);
            $volume->volume_30d = $volume_30d;
            $volume->save();
            dump($volume_30d);
            // $ticker->volume_30d = $volume_30d;
            //$ticker->save();

            //sleep(1);

            $cnt++;
            if ($cnt % 8 == 0) {
                dump('10');
                sleep(10);
            }
        }

        $cnt = 0;
        foreach ($this->shouldUpdateArr as $ticker) {
            $volume_30d = $this->updateVolume($kucoin, $ticker);

            $volume = CoinVolume::firstOrNew(['ticker_id' =>  $ticker->id]);
            $volume->volume_30d = $volume_30d;
            $volume->save();
            dump($volume_30d);
            $cnt++;
            if ($cnt % 8 == 0) {
                dump('10');
                sleep(10);
            }
        }



        return Command::SUCCESS;
    }

    public function updateVolume($kucoin, $ticker)
    {
        $symbol = $ticker->symbol->symbol;

        $volume_30d = $this->getVolume30($kucoin, $symbol, now()->timestamp);

        if ($volume_30d == 0) {
            while ($volume_30d == 0) {
                $volume_30d = $this->getVolume30($kucoin, $symbol, now()->timestamp);
                dump('sleep 10');
                sleep(10);
            }
        }

        return $volume_30d;
    }
}
