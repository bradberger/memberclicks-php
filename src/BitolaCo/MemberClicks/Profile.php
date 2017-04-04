<?php

namespace BitolaCo\MemberClicks;

use \GuzzleHttp\Client as HTTPClient;
use \BitolaCo\MemberClicks\UserAttribute;
use \JsonSerializable;

class Profile implements JsonSerializable {

    public $attributes;

    function __construct(Array $params = [])
    {
        $this->attributes = [];
        foreach($params as $k => $v) {
            $this->__set($this->normalizeKey($k), $v);
        }
    }

    function __get($name)
    {
        return array_key_exists($this->normalizeKey($name), $this->attributes) ? $this->attributes[$this->normalizeKey($name)] : null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$this->normalizeKey($name)] = $value;
    }

    public function jsonSerialize() {
        return $this->attributes;
    }

    private function normalizeKey($key)
    {
        $key = strtolower(trim($key, '[] .-_?*'));
        $key = str_replace([' | ', ' '], '_', $key);
        return $key;
    }

}
