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
        $perPage =  request('perPage', 100);

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
        })

            //->with('symbol')
            ->paginate($perPage)->withQueryString();
        return Inertia::render('Statistics/Index', [
            'items' => $tickers,
        ]);
    }
}
