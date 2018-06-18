<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents\EntryDetail;

class XmlDocumentExportAsFileEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $all_count = EntryDetail::count();
        $processed_count = 0;
        echo 'all:'.$all_count.PHP_EOL;
        for ($i=0; $i < $all_count+1000; $i=$i+1000) {
            $entries = EntryDetail::offset($i)->limit(1000)->get();
            echo 'selected from '.($i+1).' '.$entries->count().' entries'.PHP_EOL;
            foreach ($entries as $entry) {
                Storage::put('entry/'.$entry->uuid, $entry->xml_document);
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
        Schema::table('entry_details', function (Blueprint $table) {
            //
        });
    }
}
