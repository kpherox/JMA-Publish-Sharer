<?php

use App\Eloquents\EntryDetail;
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

        $all_count = EntryDetail::count();
        if (! $all_count) {
            return;
        }

        $details_last_id = EntryDetail::select('id')
                ->orderBy('id', 'desc')
                ->limit(1)->first()->id;

        $processed_count = 0;
        echo 'all count is '.$all_count.PHP_EOL;
        for ($i = 0; $i < $details_last_id + 1000; $i += 1000) {
            $details = EntryDetail::whereRaw('id BETWEEN '.($i + 1).' AND '.($i + 1000))->get();
            foreach ($details as $detail) {
                $entry = $detail->entry;
                if (! $entry) {
                    continue;
                }

                $detail->observatory_name = $entry->observatory_name;
                $detail->headline = $entry->headline;
                $detail->updated = $entry->updated;
                $detail->save();

                $processed_count++;
                if ($processed_count % 100 == 0) {
                    echo $processed_count.' of '.$all_count.' records processed.'.PHP_EOL;
                }
            }
        }
    }
}
