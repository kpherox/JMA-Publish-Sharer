<?php

if (! function_exists('formatToHTML')) {
    function formatToHTML(string $text)
    {
        $html = '<p class="card-text">'.$text.'</p>';
        $html = preg_replace('/\n/', '<br/>', $html);

        return $html;
    }
}

if (! function_exists('formatIppanhoText')) {
    function formatIppanhoText(string $text)
    {
        $html = formatToHtml($text);

        $count = 0;

        collect([
            [
                'firstPos' => mb_strpos($html, '［'),
                'brackets' => ['［', '］'],
            ],
            [
                'firstPos' => mb_strpos($html, '【'),
                'brackets' => ['【', '】'],
            ],
            [
                'firstPos' => mb_strpos($html, '＜'),
                'brackets' => ['＜', '＞'],
            ],
        ])->filter(function ($item) {
            return $item['firstPos'] !== false;
        })->sortBy('firstPos')->each(function ($item) use (&$html, &$count) {
            $heading = data_get($item, 'brackets.0').'(.+?)'.data_get($item, 'brackets.1');

            $h4 = 'h'.(4 + $count);

            $h4Opening = '<'.$h4.' class="'.($count === 0 ? 'card-title pb-2 mb-2 border-bottom' : 'card-subtitle mt-3').'">';
            $h4Closing = '</'.$h4.'><p class="card-text">';

            $html = preg_replace('/<p class="card-text">(　)?'.$heading.'(　)?<br\/>/', $h4Opening.'\2'.$h4Closing, $html);
            $html = preg_replace('/<br\/>(　)?'.$heading.'(　)?<br\/>/', '</p>'.$h4Opening.'\2'.$h4Closing, $html);
            $count++;
        });
        $html = preg_replace('/<br\/>(?!　)/', '', $html);

        return $html;
    }
}
