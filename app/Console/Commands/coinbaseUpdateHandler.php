<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use Illuminate\Console\Command;
use MockingMagician\CoinbaseProSdk\CoinbaseFacade;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\WebsocketRunner;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\ErrorMessage;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\L2UpdateMessage;
use MockingMagician\CoinbaseProSdk\Contracts\Websocket\SubscriberAuthenticationAwareInterface;

class coinbaseUpdateHandler extends Command
{





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

        $tickers = Ticker::select(['id', 'symbol_id', 'max_last24', 'max_last'])->whereHas('symbol', function ($query) {
            $query->where('exchanger', 'coinbase');
        })->with('symbol')->get();


        foreach ($tickers  as $t) {
            $symbolStr = $t->symbol->base . '-' . $t->symbol->quote;
            $this->symbolsStrings[] =  $symbolStr;
            $this->tickersArray[$symbolStr] = $t;
        }

        dump($this->tickersArray);
        //dd('1');

        $websocket = CoinbaseFacade::createUnauthenticatedWebsocket();

        $subscriber = $websocket->newSubscriber();
        $subscriber->activateChannelLevel2(true, $this->symbolsStrings);

        $websocket->run($subscriber, function ($runner) {
            while ($runner->isConnected()) {
                $message = $runner->getMessage();
                if ($message instanceof ErrorMessage) {
                    throw new Exception($message->getMessage());
                    // or break or what you want
                }

                if ($message instanceof L2UpdateMessage) {
                    $productId = $message->getProductId();
                    $changes = $message->getChanges();

                    foreach ($changes as $change) {
                        $side = $change->getSide();
                        if ($side == 'sell') {
                            $price = $change->getPrice();
                            //if ($productId == 'ETH-USD') {
                            $ticker = $this->tickersArray[$productId];
                            if ($price > $ticker->max_last) {
                                dump($price);
                                dump($ticker->max_last);
                                $ticker->max_last = $price;
                                $ticker->max_cnt = $ticker->max_cnt + 1;
                                $ticker->save();
                                $this->tickersArray[$productId] = $ticker;
                                dump($productId);
                                dump($ticker->max_cnt);
                            }

                            // }
                        }
                    }

                    continue;
                }
            }
        });

        return Command::SUCCESS;
    }
}
