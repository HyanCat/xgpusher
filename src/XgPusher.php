<?php

namespace ElfSundae\XgPush;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ElfSundae\XgPush\Pusher
 */
class XgPusher extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'xgpusher';
    }
}
