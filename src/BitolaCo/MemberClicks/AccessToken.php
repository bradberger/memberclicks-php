<?php

namespace BitolaCo\MemberClicks;

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
class AccessToken {

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

    public function getAttrKey(string $name): string {
        switch (str_replace(['_', ' '], '', strtolower($name))) {
        case "accesstoken":
            return "access_token";
        case "tokentype":
            return "token_type";
        case "expiresin":
            return "expires_in";
        case "scope":
            return "scope";
        case "serviceId":
            return "service_id";
        case "userid":
            return "user_id";
        case "jti":
            return "jti";
        default:
            return "";
        }
    }
}
