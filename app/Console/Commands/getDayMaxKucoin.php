<?php

namespace App\Console\Commands;

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


        $insertData = [];
        foreach ($kucoin->fetchTickers() as  $ticker) {

            $symbol =  $ticker['info']['symbol'];

            if (!isset($dbSymbolsKucoin[$symbol])) continue;
            $insertData[] = [
                'symbol_id' => $dbSymbolsKucoin[$symbol],
                'max_last24' => $ticker['high'],
                'max_last' => $ticker['high'],
                'max_cnt' => 0,
                'volume_24h' => 0,
                'volume_30d' => 0,
                //'vol_curent' => $ticker['baseVolume']
            ];
        }

        Ticker::upsert($insertData, ['symbol_id'], ['max_last24', 'max_last', 'max_cnt']);


        return Command::SUCCESS;
    }
}
