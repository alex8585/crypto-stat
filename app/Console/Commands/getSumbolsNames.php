<?php

namespace App\Console\Commands;

use App\Models\Symbol;
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

    public function getCoinlist()
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
    public function handle()
    {
        $client = new CoinGeckoClient();
        $coins = $client->coins()->getList();


        $coinsNames = [];
        foreach ($coins as $coin) {
            $name = $coin['name'];
            $symbol = strtoupper($coin['symbol']);
            $coinsNames[$symbol] = $name;
        }


        $coinsNames2 = $this->getCoinlist();


        $symbols = Symbol::select(['id', 'base', 'base2'])->get();
        foreach ($symbols as $symbol) {
            if (isset($coinsNames[$symbol->base])) {
                $symbol['full_name'] = $coinsNames[$symbol->base];
                $symbol->save();
            } else if (isset($coinsNames[$symbol->base2])) {
                $symbol['full_name'] = $coinsNames[$symbol->base2];
                $symbol->save();
            } else if ((isset($coinsNames2[$symbol->base]))) {
                $symbol['full_name'] = $coinsNames2[$symbol->base];
                $symbol->save();
            } else if (isset($coinsNames2[$symbol->base2])) {
                $symbol['full_name'] = $coinsNames2[$symbol->base2];
                $symbol->save();
            } else {
                $symbol->delete();
            }
        }


        return Command::SUCCESS;
    }
}
