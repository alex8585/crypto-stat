<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class Symbol extends Model
{
    use HasFactory;

    protected $appends = [
        'name',
    ];

    public function ticker()
    {
        return $this->hasOne(Ticker::class);
    }

    public function getNameAttribute()
    {
        return  $this->base . '/' . $this->quote;
    }
}
