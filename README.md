# Tencent Xinge Pusher

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elfsundae/xgpusher.svg?style=flat-square)](https://packagist.org/packages/elfsundae/xgpusher)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/ElfSundae/xgpusher/master.svg?style=flat-square)](https://travis-ci.org/ElfSundae/xgpusher)
[![StyleCI](https://styleci.io/repos/94719158/shield)](https://styleci.io/repos/94719158)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/bf534253-a172-4e2d-a3bb-91f49e4cc85a.svg?style=flat-square)](https://insight.sensiolabs.com/projects/bf534253-a172-4e2d-a3bb-91f49e4cc85a)
[![Quality Score](https://img.shields.io/scrutinizer/g/ElfSundae/xgpusher.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/xgpusher)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ElfSundae/xgpusher/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/ElfSundae/xgpusher/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/elfsundae/xgpusher.svg?style=flat-square)](https://packagist.org/packages/elfsundae/xgpusher)

## Installation

```sh
$ composer require elfsundae/xgpusher
```

## Laravel Integration

Register the service provider by adding the following to the `providers` array in `config/app.php`:

```php
ElfSundae\XgPush\PusherServiceProvider::class,
```

Add "xgpush" configuration in `config/services.php` of your app:

```php
'xgpush' => [
    'key' => env('XGPUSH_KEY'),
    'secret' => env('XGPUSH_SECRET'),
    'environment' => env('XGPUSH_ENVIRONMENT', env('APP_ENV')),
    'custom_key' => 'my',
    'account_prefix' => 'user',
],
```

## Testing

```sh
$ composer test
```

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).
