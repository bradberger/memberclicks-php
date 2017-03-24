<?php

namespace BitolaCo\MemberClicks;

use \JsonSerializable;
/*
{
  "access_token": <accessToken>,
  "token_type": <tokenType>,
  "expires_in": <expiresIn>,
  "scope": <scope>,
  "serviceId": <serviceId>,
  "userId": <userId>,
  "jti": <jti>
}
*/
class AccessToken implements JsonSerializable {

    public $attributes;

    function __construct(Array $params = [])
    {
        $this->attributes = [];
        foreach($params as $k => $v) {
            $this->__set($k, $v);
        }
    }

    function __get(string $name)
    {
        $key = $this->getAttrKey($name);
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return null;
    }

    public function __set(string $name, $value)
    {
        if ($key = $this->getAttrKey($name)) {
            $this->attributes[$key] = $value;
        }
    }

    public function jsonSerialize() {
        return $this->attributes;
    }

    public function getAttrKey(string $name): string {
        switch ($name) {
        case "access_token":
        case "accessToken":
            return "access_token";
        case "token_type":
        case "tokenType":
            return "token_type";
        case "expires_in":
        case "expiresIn":
            return "expires_in";
        case "scope":
            return "scope";
        case "service_id":
        case "serviceId":
            return "service_id";
        case "user_id":
        case "userId":
            return "user_id";
        case "refresh_token":
        case "refreshToken":
            return "refresh_token";
        case "jti":
            return "jti";
        default:
            return $name;
        }
    }
}
