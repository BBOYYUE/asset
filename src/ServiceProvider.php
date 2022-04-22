<?php


namespace Bboyyue\Asset;


use Bboyyue\Asset\Commands\AssetDaemonCommand;
use Bboyyue\Asset\Commands\AssetListenCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(){
        if(!is_file(config_path('bboyyue-asset.php'))){
            $this->publishes([
                __DIR__ . '/../config/bboyyue-asset.php' => config_path('bboyyue-asset.php'),
            ], 'config');
        }
        if (!class_exists('CreateAssetTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_asset_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_asset_table.php'),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bboyyue-asset.php', 'bboyyue-asset');
        $this->registerCommands();
    }

    protected function registerCommands(){
        $this->app->bind('command.asset:daemon', AssetDaemonCommand::class);
        $this->app->bind('command.asset:listen', AssetListenCommand::class);
        $this->commands([
            'command.asset:daemon',
            'command.asset:listen',
        ]);
    }
}