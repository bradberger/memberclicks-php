<?php

namespace BitolaCo\MemberClicks;

class ProfileTest extends BaseTest
{
    public function testJsonSerialize()
    {
        $profile = new Profile([
            'profile_id' => 1
        ]);
        $this->assertEquals('{"profile_id":1}', json_encode($profile));
    }
}
