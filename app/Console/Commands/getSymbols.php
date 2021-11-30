<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class getSymbols extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_symbols';

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

    public function getKucoin()
    {
        $kucoin = new \ccxt\kucoin();
        $symbols = $kucoin->load_markets();

        //$now = now();
        $insertData = [];
        $symbolsArr = [];
        foreach ($symbols as $symbol) {

            if ($symbol['quote'] == 'USDT') {
                $symbolStr = $symbol['info']['symbol'];
                $symbolsArr[] =  $symbolStr;
                $insertData[] = [
                    'symbol' =>  $symbolStr,
                    'exchanger' => 'kucoin',
                    'base' => $symbol['baseId'],
                    'quote' => $symbol['quoteId'],
                    //'created_at' => $now,
                    //'updated_at' => $now,
                ];
            }
        }

        try {
            Symbol::upsert($insertData, ['symbol', 'exchanger'], ['base', 'quote']);
            Symbol::where('exchanger', 'kucoin')->whereNotIn('symbol', $symbolsArr)->delete();
        } catch (QueryException $e) {
            echo $e;
        }
    }

    public function getCoinbase()
    {
        //coinbase
        $coinbase = new \ccxt\coinbase();
        $symbols = $coinbase->load_markets();

        // $now = now();
        $insertData = [];
        $symbolsArr = [];
        foreach ($symbols as $symbol) {
            if ($symbol['quote'] == 'USD') {
                $symbolsArr[] =  $symbol['id'];
                $insertData[] = [
                    'symbol' => $symbol['id'],
                    'exchanger' => 'coinbase',
                    'base' => $symbol['baseId'],
                    'quote' => $symbol['quoteId'],
                    //'created_at' => $now,
                    //'updated_at' => $now,
                ];
            }
        }

        try {
            Symbol::upsert($insertData, ['symbol', 'exchanger'], ['base', 'quote']);
            Symbol::where('exchanger', 'coinbase')->whereNotIn('symbol', $symbolsArr)->delete();
        } catch (QueryException $e) {
            echo $e;
        }
    }

    public function handle()
    {

        $this->getCoinbase();
        // $this->getKucoin();

        return Command::SUCCESS;
    }
}
