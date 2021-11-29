<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;


class getDayMax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_day_max';

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


    public function getSymbolDayMax($candles)
    {
        $firstElem = reset($candles);
        $dayMax = $firstElem[2];
        foreach ($candles as $candle) {
            if ($dayMax < $candle[2]) {
                $dayMax = $candle;
            }
        }
        return $dayMax;
    }

    public function getCandles($symbol, $timestamp)
    {

        $client = new \GuzzleHttp\Client();

        $now = Carbon::createFromTimestamp($timestamp);
        $nowSub24 = Carbon::createFromTimestamp($timestamp)->subHour(24);

        $url = "https://api.exchange.coinbase.com/products/{$symbol}/candles";
        $response = $client->request('GET', $url, [
            'query' => [
                'granularity' => 21600,
                'start' => $nowSub24->toDateTimeString(),
                'end' =>   $now->toDateTimeString()
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $candles = json_decode($response->getBody()->getContents());
        return $candles;
    }

    public function handle()
    {

        $kucoin = new \ccxt\kucoin();
        $coinbase = new \ccxt\coinbase();


        $cbTimestamp = $coinbase->seconds();
        $candles = $this->getCandles("ETH-USDT", $cbTimestamp);
        dump($candles);
        $max24 = $this->getSymbolDayMax($candles);
        dd($max24);


        $dbSymbolsKucoin = Symbol::select(['id', 'symbol'])->where('exchanger', 'kucoin')->get()->pluck('id', 'symbol');
        $dbSymbolsCoinbase = Symbol::select(['id', 'symbol'])->where('exchanger', 'coinbase')->get()->pluck('id', 'symbol');

        // foreach ($symbols as $symbol) {
        //     usleep($kucoin->rateLimit * 1000);
        //     dd($kucoin->fetch_ohlcv('ETH/USDT', '1h', null, 24));
        // }

        $insertData = [];
        foreach ($dbSymbolsCoinbase as $symbol => $symbolId) {
            usleep($coinbase->rateLimit * 1000);
            dump($symbol);
            $ticker = $coinbase->fetch_ohlcv($symbol, '1h', null, 24);
            dump($ticker);
            $insertData[] = [
                'symbol_id' => $symbolId,
                'max_last24' => $ticker['high'],
                'max_last' => $ticker['high'],
                'max_cnt' => 0,
            ];
        }


        Ticker::upsert($insertData, ['symbol_id'], ['max_last24', 'max_last', 'max_cnt']);



        $insertData = [];
        foreach ($kucoin->fetchTickers() as $symbol => $ticker) {
            if (!isset($dbSymbolsKucoin[$symbol])) continue;
            $insertData[] = [
                'symbol_id' => $dbSymbolsKucoin[$symbol],
                'max_last24' => $ticker['high'],
                'max_last' => $ticker['high'],
                'max_cnt' => 0,
            ];
        }
        Ticker::upsert($insertData, ['symbol_id'], ['max_last24', 'max_last', 'max_cnt']);








        return Command::SUCCESS;
    }
}
