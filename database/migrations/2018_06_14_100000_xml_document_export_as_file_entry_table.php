<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents\Entry;

class XmlDocumentExportAsFileEntryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $all_count = Entry::count();
        $entries_last_id = Entry::select('id')
                ->orderBy('id', 'desc')
                ->limit(1)->first()->id;

        $processed_count = 0;
        echo 'all:'.$all_count.PHP_EOL;
        for ($i=0; $i < $entries_last_id+1000; $i+=1000) {
            $entries = Entry::whereRaw('id BETWEEN '.($i+1).' AND '.($i+1000))->get();
            echo 'selected from '.($i+1).' '.$entries->count().' entries'.PHP_EOL;
            foreach ($entries as $entry) {
                Storage::put('entry/'.$entry->uuid, $entry->xml_document);
                $entry->xml_document = null;
                $entry->save();
                $processed_count++;
                if ($processed_count % 100 == 0) {
                    echo $processed_count.' entries processed. all:'.$all_count.PHP_EOL;
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
        $all_count = Entry::count();
        $entries_last_id = Entry::select('id')
                ->orderBy('id', 'desc')
                ->limit(1)->first()->id;

        $processed_count = 0;
        echo 'all:'.$all_count.PHP_EOL;
        for ($i=0; $i < $entries_last_id+1000; $i+=1000) {
            $entries = Entry::whereRaw('id BETWEEN '.($i+1).' AND '.($i+1000))->get();
            echo 'selected from '.($i+1).' '.$entries->count().' entries'.PHP_EOL;
            foreach ($entries as $entry) {
                $entry->xml_document = Storage::get('entry/'.$entry->uuid);
                $entry->save();
                $processed_count++;
                if ($processed_count % 100 == 0) {
                    echo $processed_count.' entries processed. all:'.$all_count.PHP_EOL;
                }
            }
        }
    }
}
