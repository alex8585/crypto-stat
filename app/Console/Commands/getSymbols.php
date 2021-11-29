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
    public function handle()
    {

        //kucoin
        $kucoin = new \ccxt\kucoin();
        $symbols = $kucoin->load_markets();

        $now = now();
        $insertData = [];
        foreach ($symbols as $symbol) {
            if ($symbol['quote'] == 'USDT') {
                $insertData[] = [
                    'symbol' => $symbol['symbol'],
                    'exchanger' => 'kucoin',
                    'base' => $symbol['base'],
                    'quote' => $symbol['quote'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        try {
            Symbol::insert($insertData);
        } catch (QueryException $e) {
            echo $e;
        }


        //coinbase
        $coinbase = new \ccxt\coinbase();
        $symbols = $coinbase->load_markets();



        $now = now();
        $insertData = [];
        foreach ($symbols as $symbol) {



            if ($symbol['quote'] == 'USD') {
                $insertData[] = [
                    'symbol' => $symbol['symbol'],
                    'exchanger' => 'coinbase',
                    'base' => $symbol['base'],
                    'quote' => $symbol['quote'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        try {
            Symbol::insert($insertData);
        } catch (QueryException $e) {
            echo $e;
        }





        return Command::SUCCESS;
    }
}
