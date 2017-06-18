<?php

namespace ElfSundae\XgPusher\Test;

use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }
}
