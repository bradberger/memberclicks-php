<?php

namespace BitolaCo\MemberClicks;

class MemberType
{
    public $name;
    public $type;
    
    public function __construct(Array $params = [])
    {
        if (array_key_exists('name', $params)) {
            $this->name = $params['name'];
        }
        if (array_key_exists('type', $params)) {
            $this->type = $params['type'];
        }
    }
}
