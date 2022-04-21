<?php


namespace Bboyyue\Asset;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(){
        if (!class_exists('CreateAssetTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_asset_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_asset_table.php'),
            ], 'migrations');
        }
    }
}