<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('googledrive', function($app, $config) {
             
            $client = \App::make("\Google_Client");
            
            $client->addScope("https://www.googleapis.com/auth/drive");
            $client->setSubject($config['account']);
            
            $service = new \Google_Service_Drive($client);
            
            $adapter = new GoogleDriveAdapter($service, $config['folder_id']);
            
            return new Filesystem($adapter);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
