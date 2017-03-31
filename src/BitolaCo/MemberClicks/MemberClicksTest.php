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

    /**
     * @depends testAuth
     */
    public function testResourceOwnerTokenAndProfile()
    {
        list($token, $err) = $this->memberclicks->resourceOwnerToken(getenv('MEMBERCLICKS_USER_USERNAME'), getenv('MEMBERCLICKS_USER_PASSWORD'));
        $this->assertNull($err);
        $this->assertTrue(!empty($token));

        list($profile, $err) = $this->memberclicks->getUserFromToken($token);
        $this->assertNull($err);
        $this->assertTrue(!empty($profile));

        list($profile2, $err) = $this->memberclicks->profile($profile->profile_id);
        $this->assertNull($err);
        $this->assertTrue(!empty($profile2));
        $this->assertEquals($profile->profile_id, $profile2->profile_id);
    }

    public function testCheckLogin()
    {
        $this->assertTrue($this->memberclicks->checkLogin(getenv('MEMBERCLICKS_USER_USERNAME'), getenv('MEMBERCLICKS_USER_PASSWORD')));
    }

}
