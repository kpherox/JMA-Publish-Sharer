<?php

namespace App\Services;

class SimpleIcons
{
    private $iconList;

    public function getIcons()
    {
        return $this->iconList;
    }

    /**
     * Create a new instance.
     *
     * @return void
    **/
    public function __construct(...$useIcons)
    {
        $iconList = collect(json_decode(\File::get(resource_path('assets/simple-icons/_data/simple-icons.json')), true)['icons']);

        foreach ($useIcons as $iconName) {
            $iconData = $iconList->filter(function ($value) use ($iconName) {
                return $value['title'] === $iconName;
            });
            $title = mb_strtolower($iconName);
            $hex = $iconData->first()['hex'];
            $source = $iconData->first()['source'];
            $svg = \File::get(resource_path('assets/simple-icons/icons/'.$title.'.svg'));
            $this->iconList[] = [
                "title" => $iconName,
                "lowerTitle" => $title,
                "hex" => $hex,
                "source" => $source,
                "svg" => $svg
            ];
        }
    }
}
