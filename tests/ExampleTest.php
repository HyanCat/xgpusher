<?php

namespace ElfSundae\XgPush\Test;

use Mockery as m;
use ElfSundae\XgPush\Pusher;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Pusher::class, new Pusher('key', 'secret'));
    }
}
