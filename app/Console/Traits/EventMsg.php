<?php

namespace App\Console\Traits;

trait EventMsg
{
    public function tickerToEventMsg($ticker)
    {
        $ticker->toArray();

        return [
            'id' => $ticker['id'],
            'symbol_id' => $ticker['symbol_id'],
            'max_last24' => (float)$ticker['max_last24'],
            'max_last' => (float)$ticker['max_last'],
            'updated_at' => (int)$ticker['updated_at'],
            'max_cnt' => (int)$ticker['max_cnt'],
            'percent' => $ticker['percent'],
            'volume_24h' => $ticker['volume_24h'],
            'volume_30d' => $ticker['volume_30d'],
            'base' => $ticker['symbol']['base'],
            'quote' => $ticker['symbol']['quote'],
            'exchanger' => $ticker['symbol']['exchanger'],
            'full_name' => $ticker['symbol']['full_name'],

        ];
    }
}
