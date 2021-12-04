<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\ClientException;


class getDayMaxCoinbase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_day_max_coinbase {first_start?}';
    protected $badSumbolsIds = [];
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get day max Coinbase';

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
                $dayMax = $candle[2];
            }
        }

        return $dayMax;
    }

    public function getCandles($symbol, $timestamp, $symbolId = null)
    {

        $client = new \GuzzleHttp\Client();

        $now = Carbon::createFromTimestamp($timestamp);
        $nowSub24 = Carbon::createFromTimestamp($timestamp)->subHour(24);


        $url = "https://api.exchange.coinbase.com/products/{$symbol}/candles";

        try {
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
        } catch (ClientException $e) {
            $this->badSumbolsIds[] = $symbolId;
            // echo $e;
            return null;
        }

        $candles = json_decode($response->getBody()->getContents());
        if (!$candles) {
            $this->badSumbolsIds[] = $symbolId;
        }
        return $candles;
    }

    public function handle()
    {

        $coinbase = new \ccxt\coinbase();
        $cbTimestamp = $coinbase->seconds();
        $dbSymbolsCoinbase = Symbol::select(['id', 'symbol', 'base', 'quote'])->where('exchanger', 'coinbase')->get();

        $insertData = [];

        // $isFirstStart = $this->argument('first_start');
        $isFirstStart = Ticker::where('id', $dbSymbolsCoinbase->first()->id)->get()->count() ? false : true;

        $now = now();
        foreach ($dbSymbolsCoinbase as $symbol) {

            if ($isFirstStart) {
                $symbolStr = $symbol['symbol'];
                dump($symbolStr);
                $candles = $this->getCandles($symbolStr, $cbTimestamp, $symbol->id);
                if (!$candles) {
                    continue;
                }
                $this->getSymbolDayMax($candles);
            }


            $insertData[] = [
                'symbol_id' => $symbol->id,
                'max_update_time' => $now,
                'max_last24' => 0,
                'max_last' => 0,
                'max_cnt' => 0,
                // 'volume_24h' => 0,
                // 'volume_30d' => 0,
            ];
        }

        Symbol::whereIn('id',  $this->badSumbolsIds)->delete();
        Ticker::upsert($insertData, ['symbol_id'], ['max_last24', 'max_last', 'max_cnt', 'max_update_time']);

        return Command::SUCCESS;
    }
}
