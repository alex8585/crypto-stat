<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use Illuminate\Console\Command;
use App\Events\TickerUpdateEvent;
use MockingMagician\CoinbaseProSdk\CoinbaseFacade;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\ErrorMessage;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\TickerMessage;
use App\Console\Traits\EventMsg;

class coinbaseUpdateHandler extends Command
{
    use EventMsg;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coinbase_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kucoin Update';

    protected  $symbolsStrings = [];
    protected  $tickersArray = [];

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

        $tickers = Ticker::whereHas('symbol', function ($query) {
            $query->where('exchanger', 'coinbase');
        })->with('symbol')->get();


        foreach ($tickers  as $t) {
            $symbolStr = $t->symbol->symbol;
            $this->symbolsStrings[] =  $symbolStr;
            $this->tickersArray[$symbolStr] = $t;
        }

        //dump($this->tickersArray);
        //dd('1');

        $websocket = CoinbaseFacade::createUnauthenticatedWebsocket();

        $subscriber = $websocket->newSubscriber();
        $subscriber->activateChannelTicker(true, $this->symbolsStrings);

        $websocket->run($subscriber, function ($runner) {
            while ($runner->isConnected()) {
                $message = $runner->getMessage();
                if ($message instanceof ErrorMessage) {
                    throw new Exception($message->getMessage());
                }

                if ($message instanceof TickerMessage) {
                    $productId = $message->getProductId();
                    $price = $message->getPrice();
                    $ticker = $this->tickersArray[$productId];
                    if ($productId == "ETH-USD") {

                        broadcast(new TickerUpdateEvent($this->tickerToEventMsg($ticker)));
                    }

                    if ($price > $ticker->max_last) {

                        dump($productId);
                        dump($ticker->max_last);
                        dump($price);
                        $ticker->max_last = $price;
                        $ticker->max_cnt = $ticker->max_cnt + 1;
                        $ticker->save();
                        $this->tickersArray[$productId] = $ticker;
                    }

                    continue;
                }
            }
        });

        return Command::SUCCESS;
    }
}
