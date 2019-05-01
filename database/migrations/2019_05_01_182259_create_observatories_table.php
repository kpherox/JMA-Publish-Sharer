<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents\Entry;

class CreateObservatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observatories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestampsTz();
        });

        $now = \Carbon\Carbon::now();

        $observatories = Entry::select('observatory_name')
            ->distinct('id', 'desc')
            ->get()->map(function ($observatory) use ($now) {
                return ['name' => $observatory->observatory_name, "created_at" => $now, "updated_at" => $now];
            });

        DB::table('observatories')->insert($observatories->toArray());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('observatories');
    }
}
