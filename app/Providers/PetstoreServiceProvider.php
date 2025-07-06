<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use App\Services\PetstoreService;

class PetstoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PetstoreService::class, function ($app) {
            $config = config('petstore');

            $client = new Client([
                'base_uri' => rtrim($config['base_uri'], '/') . '/',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'api_key' => $config['api_key'],
                ],
            ]);

            return new PetstoreService($client);
        });
    }
}
