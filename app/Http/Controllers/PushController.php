<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Subscribe Check JMA
     *
     * @return \Illuminate\Http\Response
     */
    function subscriber(Request $request) {
        // Subscribe check
        $hubMode = $request->input('hub_mode');
        $hubChallenge = $request->input('hub_challenge');

        if ($hubmode == 'subscribe' || $hubmode == 'unsubscribe') {
            return response($hubChallenge, 200)->header('Content-Type', 'text/plain');
        } else {
            return response('Not Found', 404)->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Recive JMA Publish
     *
     * @return \Illuminate\Http\Response
     */
    function receiveFeed(Request $request) {
        //
    }
}
