<?php

namespace App\Services;

class TwitterUtilitiesService
{
   protected $config = [];

   public function __construct()
   {
      $this->setConfig([
         'consumer_key' => env('TWITTER_KEY'),
         'consumer_secret' => env('TWITTER_SECRET'),
      ]);
   }

   protected function setConfig( $config = [] )
   {
      $this->config = array_replace_recursive( $this->config, $config );
      Twitter::reconfig( $this->config );
   }

   public function setToken( $token, $secret )
   {
      $this->setConfig([
         'token' => $token,
         'secret' => $secret,
      ]);
   }
}
