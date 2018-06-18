<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Eloquents;


class NormalizeEntryDetailParentTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $all_count = Eloquents\EntryDetail::count();
        $processed_count = 0;
        echo "all count is ".$all_count.PHP_EOL;
        for ($i=0; $i < $all_count+1000 ; $i=$i+1000) { 
            $details = Eloquents\EntryDetail::offset($i)->limit(1000)->get();
            foreach ($details as $detail) {
                $entries = Eloquents\Entry::where('observatory_name', $detail->observatory_name)->where('headline', $detail->headline)->where('updated', $detail->updated)->orderBy('id')->get();
                $detail->entry_id = $entries->first()->id;
                $detail->save();
                if (count($entries) > 1) {
                    $entries->pull(0);
                    foreach ($entries as $entry_shoud_delete) {
                        $entry_shoud_delete->delete();
                    }
                }
                $processed_count++;
                if ($processed_count % 100 == 0) {
                    echo $processed_count.' of '.$all_count.' records processed.'.PHP_EOL;
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
