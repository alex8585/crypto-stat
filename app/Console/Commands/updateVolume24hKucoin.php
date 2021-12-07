<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\Ticker;
use Illuminate\Console\Command;

class updateVolume24hKucoin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'volume_24h_kucoin';

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
        $kucoin = new \ccxt\kucoin();
        $dbSymbolsKucoin = Symbol::select(['id', 'symbol'])->where('exchanger', 'kucoin')->get()->pluck('id', 'symbol');

        $insertData = [];
        foreach ($kucoin->fetchTickers() as  $ticker) {

            $symbol =  $ticker['info']['symbol'];

            if (!isset($dbSymbolsKucoin[$symbol])) continue;


            $symbol_id = $dbSymbolsKucoin[$symbol];

            $tickerOb = Ticker::where('symbol_id', $symbol_id)->first();
            $tickerOb->quote_volume_24h = $ticker['quoteVolume'];
            $tickerOb->save();
            //Ticker::update($insertData, ['symbol_id'], ['quote_volume_24h']);
        }




        return Command::SUCCESS;
    }
}
