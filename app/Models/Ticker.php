<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Symbol;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticker extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];




    protected $appends = [
        'percent',

    ];

    public function symbol()
    {
        return $this->belongsTo(Symbol::class);
    }

    public function getPercentAttribute()
    {

        if (!$this->max_last24 && !$this->max_last) {
            return 0;
        }

        return round($this->calcPercents($this->max_last, $this->max_last24), 2);
    }
}
