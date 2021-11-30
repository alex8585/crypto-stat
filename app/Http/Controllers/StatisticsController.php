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
        $perPage =  request('perPage', 5);
        //'items' => TgUser::sort($sort, $direction)->paginate($perPage)->withQueryString(),
        $tickers = Ticker::sort('max_cnt', 'desc')->select([
            'id',
            'symbol_id',
            'max_last24',
            'max_last',
            'updated_at',
            'max_cnt',
        ])
            ->with('symbol')->paginate($perPage)->withQueryString();

        return Inertia::render('Statistics/Index', [
            'items' => $tickers,
        ]);
    }
}
