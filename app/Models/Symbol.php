<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;

class Symbol extends Model
{
    use HasFactory;


    public function ticker()
    {
        return $this->hasOne(Ticker::class);
    }
}
