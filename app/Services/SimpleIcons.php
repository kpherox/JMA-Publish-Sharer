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
    **/
    public function __construct()
    {
        $json = \File::get(resource_path('assets/simple-icons/_data/simple-icons.json'));
        $iconList = collect(json_decode($json, true)['icons']);
        $providers = collect(config('services'))->filter(function ($provider) {
            return data_get($provider, 'simple_icons', null);
        });

        $icons = $iconList->filter(function ($value) use ($providers) {
                return $providers->contains('simple_icons', $value['title']);
            })->map(function ($value) use ($providers) {
                $iconName = $value['title'];
                $provider = $providers->search(function ($provider) use ($iconName) {
                    return data_get($provider, 'simple_icons', null) === $iconName;
                });
                data_set($value, 'title', config('services.'.$provider.'.name'));
                data_set($value, 'provider', $provider);
                data_set($value, 'svg', \File::get(resource_path('assets/simple-icons/icons/'.$provider.'.svg')));
                return $value;
            })->sort(function ($a, $b) use ($providers) {
                return $providers->keys()->search($a['provider']) > $providers->keys()->search($b['provider']);
            })->all();

        $this->setIcons($icons);
    }
}
