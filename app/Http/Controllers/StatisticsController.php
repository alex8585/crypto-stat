<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Ticker;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{


    public function index()
    {
        //$direction =  request('direction', 'asc');
        //$sort =  request('sort', 'id');

        return Inertia::render('Statistics/Index', [
            'items' => $this->getTickersFromDb(),
        ]);
    }

    public function getTickers()
    {
        //$page =  request('page', 1);
        //return $page;

        return $this->getTickersFromDb();
    }


    private function getTickersFromDb()
    {
        $perPage =  request('perPage', 50);

        $tickers = Ticker::sort('max_cnt', 'desc')->select([
            'tickers.id',
            'tickers.symbol_id',
            'tickers.max_last24',
            'tickers.max_last',
            'tickers.updated_at',
            'tickers.max_cnt',
            'symbols.base',
            'symbols.quote',
            'symbols.exchanger',
        ])->join('symbols', function ($q) {
            $q->on('symbols.id', '=', 'tickers.symbol_id');
        })->paginate($perPage)->withQueryString();

        return  $tickers;
    }
}
