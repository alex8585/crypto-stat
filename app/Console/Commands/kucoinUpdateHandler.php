<?php

namespace App\Console\Commands;

use KuCoin\SDK\Auth;
use App\Models\Ticker;
use React\EventLoop\Factory;
use Ratchet\Client\WebSocket;
use Illuminate\Console\Command;
use React\EventLoop\LoopInterface;
use KuCoin\SDK\PrivateApi\WebSocketFeed;
use KuCoin\SDK\Exceptions\BusinessException;

class kucoinUpdateHandler extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kucoin_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kucoin Update';

    protected $symbolsStrings = [];
    protected $tickersArray = [];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function setTickersFromDb()
    {
        $tickers = Ticker::select(['id', 'symbol_id', 'max_last24', 'max_last'])->whereHas('symbol', function ($query) {
            $query->where('exchanger', 'kucoin');
        })->with('symbol')->get();

        foreach ($tickers  as $t) {
            $symbolStr = $t->symbol->symbol;
            $this->symbolsStrings[] =  $symbolStr;
            $this->tickersArray[$symbolStr] = $t;
        }
        //$this->tickerTopic = "/market/ticker:" . implode(',', $this->symbolsStrings);
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $this->setTickersFromDb();



        $auth = null;
        $api = new WebSocketFeed($auth);
        $query = ['connectId' => uniqid('', true)];
        $channels = [
            ['topic' => '/market/ticker:all'],
        ];

        try {
            $api->subscribePublicChannels($query, $channels, function (array $message, WebSocket $ws, LoopInterface $loop) use ($api) {
                var_dump($message);

                // $ticker = $this->tickersArray[$productId];
                // if ($price > $ticker->max_last) {
                //     dump($productId);
                //     dump($ticker->max_last);
                //     dump($price);
                //     $ticker->max_last = $price;
                //     $ticker->max_cnt = $ticker->max_cnt + 1;
                //     $ticker->save();
                //     $this->tickersArray[$productId] = $ticker;
                //     // dump($ticker->max_cnt);
                // }

            }, function ($code, $reason) {
                echo "OnClose: {$code} {$reason}\n";
            });
        } catch (BusinessException $e) {
            dump($e);
        }


        return Command::SUCCESS;
    }
}
