<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\ClientException;


class getDayMax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_day_max';
    protected $badSumbolsIds = [];
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
                'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                    echo ($stats->getHandlerStats()['redirect_url']);
                }
            ]);
        } catch (ClientException $e) {
            $this->badSumbolsIds[] = $symbolId;
            echo $e;
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


        // $symbols = Symbol::with('ticker')->get();
        // foreach ($symbols as $s) {
        //     if (!$s->ticker) {
        //         dump($s);
        //     }
        // }

        // dd('1');
        $kucoin = new \ccxt\kucoin();
        $coinbase = new \ccxt\coinbase();


        $cbTimestamp = $coinbase->seconds();


        $dbSymbolsKucoin = Symbol::select(['id', 'symbol'])->where('exchanger', 'kucoin')->get()->pluck('id', 'symbol');
        $dbSymbolsCoinbase = Symbol::select(['id', 'symbol', 'base', 'quote'])->where('exchanger', 'coinbase')->get();

        // foreach ($symbols as $symbol) {
        //     usleep($kucoin->rateLimit * 1000);
        //     dd($kucoin->fetch_ohlcv('ETH/USDT', '1h', null, 24));
        // }

        $insertData = [];
        foreach ($dbSymbolsCoinbase as $symbol) {

            $symbolStr = $symbol['base'] . '-' . $symbol['quote'];
            usleep($coinbase->rateLimit * 0);

            dump($symbolStr);
            //$cbTimestamp = $coinbase->seconds();
            $candles = $this->getCandles($symbolStr, $cbTimestamp, $symbol->id);

            if (!$candles) {
                continue;
            }
            $max24 = $this->getSymbolDayMax($candles);
            dump($max24);
            $insertData[] = [
                'symbol_id' => $symbol->id,
                'max_last24' => $max24,
                'max_last' => $max24,
                'max_cnt' => 0,
            ];
            // dump($insertData);
        }

        //dump($this->badSumbolsIds);
        Symbol::whereIn('id',  $this->badSumbolsIds)->delete();
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
