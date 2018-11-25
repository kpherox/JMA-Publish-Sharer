<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->bigInteger('entry_id');
            $table->uuid('uuid')->unique();
            $table->string('kind_of_info');
            $table->string('observatory_name');
            $table->string('headline');
            $table->string('url');
            $table->dateTimeTz('updated');
            $table->timestampsTz();
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
