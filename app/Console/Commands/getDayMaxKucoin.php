<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Console\Command;



class getDayMaxKucoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_day_max_kucoin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Day max Kucoin';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $kucoin = new \ccxt\kucoin();
        $dbSymbolsKucoin = Symbol::select(['id', 'symbol'])->where('exchanger', 'kucoin')->get()->pluck('id', 'symbol');
        $now = now();
        $insertData = [];
        foreach ($kucoin->fetchTickers() as  $ticker) {

            $symbol =  $ticker['info']['symbol'];

            if (!isset($dbSymbolsKucoin[$symbol])) continue;

            // if ($ticker['symbol'] == 'BTC/USDT') {
            //     dd($ticker);
            // }
            $insertData[] = [
                'symbol_id' => $dbSymbolsKucoin[$symbol],
                'max_last24' => $ticker['high'],
                'max_last' => $ticker['high'],
                'max_cnt' => 0,
                'volume_24h' => $ticker['baseVolume'],
                'max_update_time' => $now,
                'quote_volume_24h' => $ticker['quoteVolume'],
            ];
        }

        Ticker::upsert($insertData, ['symbol_id'], ['max_last24', 'max_last', 'max_cnt', 'volume_24h', 'max_update_time', 'quote_volume_24h']);


        return Command::SUCCESS;
    }
}
