<?php

namespace App\Services;

class SimpleIcons
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(...$useIcons)
    {
        foreach ($useIcons as $iconName) {
            $iconList = json_decode(file_get_contents(resource_path('assets/simple-icons/_data/simple-icons.json')), true)['icons'];
            $iconKey = array_search($iconName, array_column($iconList, 'title'));
            $iconData = $iconList[$iconKey];
            $title = mb_strtolower($iconName);
            $hex = $iconData['hex'];
            $source = $iconData['source'];
            $svg = file_get_contents(resource_path('assets/simple-icons/icons/'.$title.'.svg'));
            $this->$title = [
                "title" => $iconName,
                "lowerTitle" => $title,
                "hex" => $hex,
                "source" => $source,
                "svg" => $svg
            ];
        }
    }
}
