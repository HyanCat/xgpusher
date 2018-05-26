<?php

namespace ElfSundae\XgPush;

use Illuminate\Support\ServiceProvider;

class PusherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('xgpusher.ios', function ($app) {
            $config = $app['config']->get('services.xgpush');

            return (new Pusher($config['ios_key'], $config['ios_secret']))
                ->setEnvironment($config['environment'])
                ->setCustomKey($config['custom_key'])
                ->setAccountPrefix($config['account_prefix']);
        });

        $this->app->bind('xgpusher.android', function ($app) {
            $config = $app['config']->get('services.xgpush');

            return (new Pusher($config['android_key'], $config['android_secret']))
                ->setEnvironment($config['environment'])
                ->setCustomKey($config['custom_key'])
                ->setAccountPrefix($config['account_prefix']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['xgpusher.ios', 'xgpusher.android'];
    }
}
