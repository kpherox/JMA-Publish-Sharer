<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents\Entry;

class DeleteSomeColomnsFromEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('uuid');
            $table->dropColumn('kind_of_info');
            $table->dropColumn('url');
            $table->dropColumn('xml_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->uuid('uuid')->nullable();
            $table->string('kind_of_info')->nullable();
            $table->string('url', 2048)->nullable();
            $table->mediumText('xml_document')->nullable();
        });

        $all_count = Entry::count();
        if (!$all_count) {
            return;
        }

        $entries_last_id = Entry::select('id')
                ->orderBy('id', 'desc')
                ->limit(1)->first()->id;

        $processed_count = 0;
        echo "all count is ".$all_count.PHP_EOL;
        for ($i=0; $i < $entries_last_id+1000; $i+=1000) {
            $entries = Entry::whereRaw('id BETWEEN '.($i+1).' AND '.($i+1000))->get();
            foreach ($entries as $entry) {
                $detail = $entry->entryDetails()->first();
                if (!$detail) {
                    continue;
                }

                $entry->uuid = $detail->uuid;
                $entry->kind_of_info = $detail->kind_of_info;
                $entry->url = $detail->url;
                $entry->save();

                $processed_count++;
                if ($processed_count % 100 == 0) {
                    echo $processed_count.' of '.$all_count.' records processed.'.PHP_EOL;
                }
            }
        }
    }
}
