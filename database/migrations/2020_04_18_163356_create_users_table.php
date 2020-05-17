<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
		    $table->engine = 'InnoDB';
		    $table->increments('user_id');
		    $table->string('username', 255);
		    $table->string('password', 255);
		    $table->string('first_name', 255);
		    $table->string('last_name', 255);
		    $table->string('email', 255);
            $table->string('tel', 255);
            $table->string('token', 255);
		    $table->string('verify', 1)->default(0);
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
        Schema::dropIfExists('users');
    }
}
