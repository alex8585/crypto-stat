<?php

namespace App\Console\Traits;

trait EventMsg
{
    public function tickerToEventMsg($ticker)
    {
        $tickerArr = $ticker->toArray();
        // dd($tickerArr);
        return [
            'id' => $ticker['id'],
            'symbol_id' => $ticker['symbol_id'],
            'max_last24' => $ticker['max_last24'],
            'max_last' => $ticker['max_last'],
            'updated_at' => (string)$ticker['updated_at'],
            'max_cnt' => $ticker['max_cnt'],
            'base' => $ticker['symbol']['base'],
            'quote' => $ticker['symbol']['quote'],
            'exchanger' => $ticker['symbol']['exchanger'],
        ];
    }
}