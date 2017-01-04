<?php

namespace BitolaCo\MemberClicks;

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected $memberclicks;

    protected function setUp()
    {
        $this->memberclicks = new MemberClicks('2406471784', 'demouser', 'demopass');
        $this->memberclicks->init();
    }

    public function testInit() {
        $this->assertNotNull($this->memberclicks);
    }
}
