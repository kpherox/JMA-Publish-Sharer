<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteSomeColumnsFromEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entry_details', function (Blueprint $table) {
            $table->dropColumn('observatory_name');
            $table->dropColumn('headline');
            $table->dropColumn('updated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entry_details', function (Blueprint $table) {
            $table->string('observatory_name');
            $table->string('headline');
            $table->dateTimeTz('updated');
        });
    }
}
