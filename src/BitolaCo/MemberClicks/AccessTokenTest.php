<?php

namespace BitolaCo\MemberClicks;

class AccessTokenTest extends BaseTest
{
    var $token;

    function setUp() {
        parent::setUp();
        $this->token = new AccessToken();
    }

    function testAttributesMagicMethods() {
        $this->token->access_token = "foobar";
        $this->assertEquals('foobar', $this->token->attributes['access_token']);
        $this->assertEquals('foobar', $this->token->access_token);
        $this->assertEquals('foobar', $this->token->accessToken);
    }

    function testGetAttrKey() {
        $this->assertEquals('access_token', $this->token->getAttrKey('accessToken'));
        $this->assertEquals('access_token', $this->token->getAttrKey('access_token'));
        $this->assertEquals('token_type', $this->token->getAttrKey('token_type'));
        $this->assertEquals('token_type', $this->token->getAttrKey('tokenType'));
    }
}
