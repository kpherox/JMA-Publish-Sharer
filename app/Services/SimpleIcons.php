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
    public function __construct()
    {
        $iconList = collect(json_decode(\File::get(resource_path('assets/simple-icons/_data/simple-icons.json')), true)['icons']);
        $providers = config('services.providers');

        foreach ($providers as $provider => $providerName) {
            $iconData = $iconList->filter(function ($value) use ($providerName) {
                return $value['title'] === $providerName;
            })->first();
            $hex = $iconData['hex'];
            $source = $iconData['source'];
            $svg = \File::get(resource_path('assets/simple-icons/icons/'.$provider.'.svg'));
            $this->iconList[] = [
                "title" => $providerName,
                "lowerTitle" => $provider,
                "hex" => $hex,
                "source" => $source,
                "svg" => $svg
            ];
        }
    }
}
