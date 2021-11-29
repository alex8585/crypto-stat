<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MockingMagician\CoinbaseProSdk\CoinbaseFacade;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\WebsocketRunner;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\ErrorMessage;
use MockingMagician\CoinbaseProSdk\Functional\Websocket\Message\L2UpdateMessage;
use MockingMagician\CoinbaseProSdk\Contracts\Websocket\SubscriberAuthenticationAwareInterface;

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
        // $kucoin = new \ccxt\kucoin();
        // $t = $kucoin->fetchTickers();
        // dd($t);
        // while (true) {
        // }

        // $websocket = CoinbaseFacade::createUnauthenticatedWebsocket();

        // $subscriber = $websocket->newSubscriber();
        // $subscriber->activateChannelLevel2(true, ['BTC-EUR']);

        // $websocket->run($subscriber, function ($runner) {
        //     /** @var WebsocketRunner $runner */
        //     while ($runner->isConnected()) {
        //         $message = $runner->getMessage();
        //         if ($message instanceof ErrorMessage) {
        //             throw new Exception($message->getMessage());
        //             // or break or what you want
        //         }

        //         if ($message instanceof L2UpdateMessage) {
        //             $productId = $message->getProductId();
        //             $time = $message->getTime();
        //             $changes = $message->getChanges();

        //             foreach ($changes as $change) {

        //                 $side = $change->getSide();
        //                 $price = $change->getPrice();

        //                 dump($productId);
        //                 dump($price);
        //             }

        //             continue;
        //         }
        //     }
        // });

        return Command::SUCCESS;
    }
}
