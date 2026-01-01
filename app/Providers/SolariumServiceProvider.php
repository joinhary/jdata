<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SolariumServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function () {

            // Tạo adapter CURL
            $adapter = new Curl();

            // Tạo event dispatcher
            $dispatcher = new EventDispatcher();

            // Config Solr
            $config = [
                'endpoint' => [
                    'default' => [
                        'host' => '127.0.0.1',
                        'port' => 8983,
                        'path' => '',
                        'core' => 'timkiemsuutra',
                        'timeout' => 30,
                    ],
                ],
            ];

            // Tạo client theo chuẩn Solarium 7.x
            return new Client($adapter, $dispatcher, $config);
        });
    }
}
