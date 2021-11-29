<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTickersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickers', function (Blueprint $table) {
            $table->id();
            $table->integer('symbol_id')->index();
            $table->float('max_last24', 16, 8)->default(0);
            $table->float('max_last', 16, 8)->default(0);
            $table->integer('max_cnt');
            $table->timestamps();
            $table->unique('symbol_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickers');
    }
}
