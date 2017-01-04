<?php

namespace BitolaCo\MemberClicks;

class EventTest extends BaseTest
{

    private $eventID = '344420';

    function testGetEvent()
    {
        list($event, $err) = $this->memberclicks->getEvent($this->eventID);
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertInstanceOf('BitolaCo\MemberClicks\Event', $event);
        $this->assertEquals($this->eventID, $event->eventId);
        $this->assertEquals('2970 Peachtree Rd Atlanta, GA 30305', $event->location);
    }

    public function testGetEvents()
    {
        list($events, $err) = $this->memberclicks->getEvents();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertTrue('array' == gettype($events));
        if (count($events)) {
            $this->assertNotEmpty($events);
            $this->assertInstanceOf('BitolaCo\MemberClicks\Event', $events[0]);
        }
    }

    function testLoadEvent() {
        $event = new Event($this->memberclicks);
        $event->eventId = $this->eventID;
        $err = $event->load();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertEquals($this->eventID, $event->eventId);
        $this->assertEquals('2970 Peachtree Rd Atlanta, GA 30305', $event->location);
    }
}
