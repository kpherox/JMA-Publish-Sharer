<?php

namespace App\Services;

class SimpleIcons
{
    /**
     * Social icon list.
     *
     * @var array
     */
    private $iconList;

    /**
     * Get private $iconList.
     *
     * @return array
     */
    public function getIcons()
    {
        return $this->iconList;
    }

    /**
     * Get private $iconList.
     *
     * @param  array $icons
     */
    public function setIcons(array $icons)
    {
        $this->iconList = $icons;
    }

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $iconList = collect(json_decode(\File::get(resource_path('assets/simple-icons/_data/simple-icons.json')), true)['icons']);
        $providers = collect(config('services.providers'));

        $icons = $iconList->filter(function ($value) use ($providers) {
            return $providers->contains($value['title']);
        })->map(function ($value) use ($providers) {
            $provider = $providers->search($value['title']);
            $value['lowerTitle'] = $provider;
            $value['svg'] = \File::get(resource_path('assets/simple-icons/icons/'.$provider.'.svg'));

            return $value;
        })->sort(function ($a, $b) use ($providers) {
            return $providers->keys()->search($a['lowerTitle']) > $providers->keys()->search($b['lowerTitle']);
        })->all();

        $this->setIcons($icons);
    }
}
