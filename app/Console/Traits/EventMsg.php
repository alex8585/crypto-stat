<?php

namespace App\Console\Traits;

trait EventMsg
{
    public function tickerToEventMsg($ticker)
    {
        //dd($ticker);
        //$ticker->toArray();
        //dump($ticker);
        return [
            'id' => $ticker['id'],
            'symbol_id' => $ticker['symbol_id'],
            'max_last24' => (float)$ticker['max_last24'],
            'max_last' => (float)$ticker['max_last'],
            'max_update_time' => (int)$ticker['max_update_time'],
            'max_cnt' => (int)$ticker['max_cnt'],
            'percent' => $ticker['percent'],
            'volumePercent' => (float)$ticker['volumePercent'],
            'volume_24h' => $ticker['volume_24h'],
            'volumeQuote24' => (string)$ticker['volumeQuote24'],
            'volume_30d' => isset($ticker['volume']['volume_30d']) ? $ticker['volume']['volume_30d'] : 0,
            'base' => $ticker['symbol']['base'],
            'quote' => $ticker['symbol']['quote'],
            'exchanger' => $ticker['symbol']['exchanger'],
            'full_name' => $ticker['symbol']['full_name'],

        ];
    }
}
