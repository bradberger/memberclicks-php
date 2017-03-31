<?php

namespace BitolaCo\MemberClicks;

class EventTest extends BaseTest
{
    public function testKeyAndId()
    {
        $event = new Event();
        $event->name = 'event name here';
        $this->assertEquals('event-name-here', $event->key());
        $this->assertEquals(hash('sha1', $event->name), $event->getId());
    }
}
