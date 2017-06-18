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
        $this->app->singleton('xgpusher', function ($app) {
            $config = $app['config']['services.xgpush'];

            return (new Pusher($config['key'], $config['secret']))
                ->setEnvironment($config['environment'])
                ->setCustomKey($config['custom_key'])
                ->setAccountPrefix($config['account_prefix']);
        });

        $this->app->alias('xgpusher', Pusher::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['xgpusher', Pusher::class];
    }
}
