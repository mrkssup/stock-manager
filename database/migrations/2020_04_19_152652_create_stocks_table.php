<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function(Blueprint $table) {
		    $table->engine = 'InnoDB';
		
		    $table->increments('stock_id');
		    $table->integer('stock_number')->nullable();
		    $table->integer('stock_number_sale')->nullable();
		    $table->integer('products_product_id');
		    $table->integer('products_user_user_id');
		    $table->timestamps();
		
        });
        // Schema::table('stocks', function($table) {
        //     $table->index(['products_product_id', 'products_user_user_id'],'fk_stocks_products1_idx');
		//     $table->foreign('products_product_id')
        //         ->references('product_id')->on('products')
        //         ->onDelete('cascade');
		//     $table->foreign('products_user_user_id')
        //         ->references('user_user_id')->on('products')
        //         ->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
