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

    function __get(string $name)
    {
        return $this->attributes[$this->normalizeKey($name)] ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->attributes[$this->normalizeKey($name)] = $value;
    }

    public function jsonSerialize() {
        return $this->attributes;
    }

    private function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key, '[] .-_?*'));
        $key = str_replace([' | ', ' '], '_', $key);
        return $key;
    }

}
