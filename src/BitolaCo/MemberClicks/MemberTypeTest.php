<?php

namespace BitolaCo\MemberClicks;

class MemberTypeTest extends BaseTest
{
    function testConstruct()
    {
        $memberType = new MemberType(['name' => 'foo', 'type' => 'bar']);
        $this->assertEquals('foo', $memberType->name);
        $this->assertEquals('bar', $memberType->type);
    }
}
