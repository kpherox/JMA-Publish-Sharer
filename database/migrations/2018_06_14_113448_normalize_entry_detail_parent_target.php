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
        if (!$all_count) {
            return;
        }

        $details_last_id = Eloquents\EntryDetail::select('id')
                ->orderBy('id', 'desc')
                ->limit(1)->first()->id;

        $processed_count = 0;
        echo "all count is ".$all_count.PHP_EOL;
        for ($i=0; $i < $details_last_id+1000; $i+=1000) {
            $details = Eloquents\EntryDetail::whereRaw('id BETWEEN '.($i+1).' AND '.($i+1000))->get();
            foreach ($details as $detail) {
                $entries = Eloquents\Entry::select('id')
                    ->where([
                        ['observatory_name', $detail->observatory_name],
                        ['headline', $detail->headline],
                        ['updated', $detail->updated],
                    ])->orderBy('id');

                if ($entries->count() > 1) {
                    $first = true;
                    foreach ($entries->get() as $entry) {
                        if ($first) {
                            $first = false;
                            $detail->entry_id = $entry->id;
                            $detail->save();
                            continue;
                        }
                        $entry->delete();
                    }
                } elseif ($detail->entry_id !== $entries->first()->id) {
                    $detail->entry_id = $entries->first()->id;
                    $detail->save();
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
    }
}
