<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Symbol;
use App\Models\CoinVolume;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticker extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'max_update_time' => 'timestamp',
    ];




    protected $appends = [
        'percent',
        'volumePercent'

    ];

    public function volume()
    {
        return $this->hasOne(CoinVolume::class);
    }

    public function symbol()
    {
        return $this->belongsTo(Symbol::class);
    }

    public function getPercentAttribute()
    {

        if (!$this->max_last24 || !$this->max_last) {
            return 0;
        }

        return round($this->calcPercents($this->max_last, $this->max_last24), 2);
    }

    public function getVolumePercentAttribute()
    {
        if (!$this->volume_24h) {
            return 0;
        }

        $vol_30  = 0;
        if ($this->relationLoaded('volume')) {
            $vol_30  = $this->volume->volume_30d;
        } else if (isset($this->volume_30d)) {
            $vol_30  = $this->volume_30d;
        }

        if (!$vol_30) {
            return 0;
        }

        return round($this->calcPercents($this->volume_24h, $vol_30 / 30), 2);
    }
}
