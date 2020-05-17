<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_places', function(Blueprint $table) {
		    $table->engine = 'InnoDB';
		    $table->increments('stock_place_id');
		    $table->string('stock_place_code', 255);
		    $table->string('stock_place_name', 255);
		    $table->string('stock_places_detail', 255);
		    $table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stockplaces');
    }
}
