<?php

use App\Eloquents\EntryDetail;
use App\Services\SimpleXML;
use Illuminate\Database\Migrations\Migration;

class ExportEventIdToEntryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            $range = [
                ($i + 1),
                ($i + 1000)
            ];
            $details = EntryDetail::whereRaw('id BETWEEN ? AND ?', $range)->get();
            echo vsprintf('from %d to %d entries selected.', $range).PHP_EOL;
            foreach ($details as $detail) {
                try {
                    $doc = $detail->xml_file;

                    $entryArray = collect((new SimpleXML($doc, true))->toArray(true));
                    $eventId = data_get($entryArray, 'Head.EventID');
                } catch (\Exception $e) {
                    \Log::info('Error caught uuid: '.$detail->uuid);
                    report($e);
                    $eventId = null;
                }

                if ($eventId) {
                    $detail->event_id = $eventId;
                    $detail->save();
                }

                $processed_count++;
                if (($processed_count) % 1000 == 0) {
                    echo($processed_count).' of '.$all_count.' records copied.'.PHP_EOL;
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
        \DB::table('entry_details')->update(['event_id' => null]);
    }
}
