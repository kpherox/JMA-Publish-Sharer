<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_details', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('entry_id');
            $table->uuid('uuid')->unique();
            $table->string('kind_of_info');
            $table->string('observatory_name');
            $table->string('headline');
            $table->string('url', 2048);
            $table->dateTimeTz('updated');
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
        Schema::dropIfExists('entry_details');
    }
}
