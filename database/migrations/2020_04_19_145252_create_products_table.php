<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function(Blueprint $table) {
		    $table->engine = 'InnoDB';
		
		    $table->increments('product_id');
		    $table->string('product_code', 255);
		    $table->string('product_name', 255);
		    $table->string('product_status', 1);
		    $table->string('product_price_buy', 255);
            $table->string('product_price_sell', 255);
            $table->string('product_unit', 255);
		    $table->string('product_volume', 255);
		    $table->integer('user_user_id')->unsigned();	
		    $table->timestamps();
        });
        // Schema::table('products', function($table) {
        //     $table->index('user_user_id','fk_product_user_idx');	
		//     $table->foreign('user_user_id')
        //         ->references('user_id')->on('users')
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
        Schema::dropIfExists('products');
    }
}
