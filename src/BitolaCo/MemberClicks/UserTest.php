<?php

namespace BitolaCo\MemberClicks;

class UserTest extends BaseTest
{

    private $userID = '21838877';
    private $attributeID = '491899';

    public function testGetUser()
    {
        list($user, $err) = $this->memberclicks->getUser($this->userID);
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertInstanceOf('BitolaCo\MemberClicks\User', $user);
        $this->assertEquals($this->userID, $user->userId);
        $this->assertEquals('demo', $user->orgId);
        $this->assertEquals('Test User', $user->contactName);
    }

    public function testUserLoad()
    {
        $user = new User($this->memberclicks);
        $user->userId = $this->userID;
        $err = $user->load();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertInstanceOf('BitolaCo\MemberClicks\User', $user);
        $this->assertEquals($this->userID, $user->userId);
        $this->assertEquals('demo', $user->orgId);
        $this->assertEquals('Test User', $user->contactName);
    }

    public function testUserGetAttribute()
    {
        $user = new User($this->memberclicks, ['userId' => $this->userID]);
        $err = $user->load();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);

        // TODO attributes are currently not set up in demo org. Need to implement this.
        list($attr, $err) = $user->getAttribute($this->attributeID);
        if (substr_count($err, '404 Not Found')) {
            return;
        }
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertInstanceOf('BitolaCo\MemberClicks\UserAttribute', $attr);
    }

    public function testUserGetAttributes()
    {
        $user = new User($this->memberclicks, ['userId' => $this->userID]);
        $err = $user->load();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);

        list($attrs, $err) = $user->getAttributes();
        $this->assertEmpty($err, 'Expected no error, got: ' . json_encode($err));
        $this->assertTrue('array' === gettype($attrs));
    }

    public function testUserGetPhoto()
    {
        $this->markTestIncomplete();
    }

    public function testUserAll()
    {
        list($users, $err) = User::all($this->memberclicks);
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertTrue(count($users) > 0);
    }
}
