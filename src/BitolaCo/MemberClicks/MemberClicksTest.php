<?php

namespace BitolaCo\MemberClicks;

use DateTime;

class MemberClicksTest extends BaseTest
{
    public function testAuth() {
        list($token, $error) = $this->memberclicks->auth();
        $this->assertNull($error);
        $this->assertFalse(empty($token));
        $this->assertTrue(count($token->access_token) > 0);
    }

    /**
     * @depends testAuth
     */
    public function testEvents() {
        list($events, $error) = $this->memberclicks->events();
        $this->assertNull($error);
        $this->assertTrue(is_array($events));
        foreach($events as $event) {
            $this->assertTrue($event instanceof Event);
            $this->assertTrue($event->date instanceof DateTime);
            $this->assertTrue(!!count($event->name));
        }
    }
}
