<?php

namespace App\Console\Commands;

use App\Models\Symbol;
use App\Models\CoinNames;

use Illuminate\Console\Command;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;


class getSumbolsNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_sumbols_names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $coinlist = [];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getCoinlistCoins()
    {

        $client = new \GuzzleHttp\Client();
        $url = "https://www.cryptocompare.com/api/data/coinlist/";

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept' => 'application/json',
                ],

            ]);
        } catch (ClientException $e) {
            dump($e);
            return [];
        }

        $coinlist = json_decode($response->getBody()->getContents());
        if (!$coinlist) {
            return [];
        }

        foreach ($coinlist->Data as $c) {
            $this->coinlist[$c->Symbol] = $c->CoinName;
        }

        return $this->coinlist;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function getCoinGeckoCoins()
    {
        $client = new CoinGeckoClient();
        $coins = $client->coins()->getList();


        $coinsNames = [];
        foreach ($coins as $coin) {
            $name = $coin['name'];
            $symbol = strtoupper($coin['symbol']);
            $coinsNames[$symbol] = $name;
        }
        return $coinsNames;
    }


    public function insertNewCoinNames($symbols, $coinsNames, $provider)
    {
        foreach ($symbols as $symbol) {
            $name = '';
            $base = '';
            if (isset($coinsNames[$symbol->base])) {
                $base = $symbol->base;
                $name =   $coinsNames[$base];
            } else if (isset($coinsNames[$symbol->base2])) {
                $base = $symbol->base2;
                $name =  $coinsNames[$base];
            }
            if ($name) {
                $coinName = CoinNames::firstOrNew(['sumbol' =>  $base]);
                if (!$coinName->exists) {
                    $coinName->provider = $provider;
                    $coinName->name = $name;
                    $coinName->sumbol =  $base;
                    $coinName->save();
                }
            }
        }
    }





    public function handle()
    {

        $symbols = Symbol::select(['id', 'base', 'base2'])->get();


        $coinGeckoCoins = $this->getCoinGeckoCoins();

        $this->insertNewCoinNames($symbols, $coinGeckoCoins, 'coingecko');

        $coinlistCoins = $this->getCoinlistCoins();

        $this->insertNewCoinNames($symbols, $coinlistCoins, 'coinlist');



        return Command::SUCCESS;
    }
}
