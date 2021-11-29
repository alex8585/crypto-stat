<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Symbol;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticker extends Model
{
    use HasFactory;


    public function symbol()
    {
        return $this->belongsTo(Symbol::class);
    }
}
