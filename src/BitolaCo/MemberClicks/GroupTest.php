<?php

namespace BitolaCo\MemberClicks;

class GroupTest extends BaseTest
{

    private $groupID = '134904';

    function testGetGroup()
    {
        list($group, $err) = $this->memberclicks->getGroup($this->groupID);
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertInstanceOf('BitolaCo\MemberClicks\Group', $group);
        $this->assertEquals($this->groupID, $group->groupID);
        $this->assertEquals('demo', $group->orgId);
    }

    public function testGetGroups()
    {
        list($groups, $err) = $this->memberclicks->getGroups();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertNotEmpty($groups);
        $this->assertTrue('array' == gettype($groups));
        $this->assertInstanceOf('BitolaCo\MemberClicks\Group', $groups[0]);
    }

    function testLoadGroup() {
        $group = new Group($this->memberclicks);
        $group->groupID = $this->groupID;
        $err = $group->load();
        $this->assertEmpty($err, 'Unexpected error: ' . $err);
        $this->assertEquals($this->groupID, $group->groupID);
        $this->assertEquals('demo', $group->orgId);
    }
}
