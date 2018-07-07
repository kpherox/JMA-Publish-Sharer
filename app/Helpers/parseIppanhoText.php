<?php

if (! function_exists('parseIppanhoText')) {
    function parseIppanhoText($text) {
        $text = '<p class="card-text">'.$text.'</p>';
        $text = preg_replace('/\n/', '<br/>', $text);

        $count = 0;

        collect([
            [
                'firstPos' => mb_strpos($text, '［'),
                'brackets' => ['［','］'],
            ],
            [
                'firstPos' => mb_strpos($text, '【'),
                'brackets' => ['【','】'],
            ],
            [
                'firstPos' => mb_strpos($text, '＜'),
                'brackets' => ['＜','＞'],
            ],
        ])->filter(function ($item) {
            return $item['firstPos'] !== false;
        })->sortBy('firstPos')->each(function ($item) use (&$text, &$count) {
            $heading = data_get($item, 'brackets.0').'(.+?)'.data_get($item, 'brackets.1');

            $h4 = 'h'.(4 + $count);

            $h4Opening = '<'.$h4.' class="'.($count === 0 ? 'card-title pb-2 mb-2 border-bottom' : 'card-subtitle mt-3').'">';
            $h4Closing = '</'.$h4.'><p class="card-text">';

            $text = preg_replace('/<p class="card-text">'.$heading.'<br\/>/', $h4Opening.'\1'.$h4Closing, $text);
            $text = preg_replace('/<br\/>'.$heading.'<br\/>/', '</p>'.$h4Opening.'\1'.$h4Closing, $text);
            $count++;
        });
        $text = preg_replace('/<br\/>(?!　)/', '', $text);

        return $text;
    }
}

