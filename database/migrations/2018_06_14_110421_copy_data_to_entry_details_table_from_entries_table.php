<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents;

class CopyDataToEntryDetailsTableFromEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $entries_count = Eloquents\Entry::count();
        $processed_count = 0;
        echo "all count is ".$entries_count.PHP_EOL;
        for ($i=0; $i < $entries_count+1000; $i=$i+1000) { 
            $entries = Eloquents\Entry::offset($i)->limit(1000)->get();
            echo 'from '.($i+1).' to '.($i+1000).' entries selected.'.PHP_EOL;
            foreach ($entries as $entry) {
                $detail = new Eloquents\EntryDetail();
                $detail->entry_id = $entry->id;
                $detail->uuid = $entry->uuid;
                $detail->kind_of_info = $entry->kind_of_info;
                $detail->url = $entry->url;
                $detail->xml_document = $entry->xml_document;
                $detail->observatory_name = $entry->observatory_name;
                $detail->headline = $entry->headline;
                $detail->updated = $entry->updated;
                $detail->save();
                $processed_count++;
                if (($processed_count) % 1000 == 0) {
                    echo ($processed_count).' of '.$entries_count.' records copied.'.PHP_EOL;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entries', function (Blueprint $table) {
            Eloquents\EntryDetail::truncate();
        });
    }
}
