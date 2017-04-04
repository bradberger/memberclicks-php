<?php

namespace BitolaCo\MemberClicks;

use DateTime;

class Event
{
    public $name;
    public $date;

    static $hashAlgorithm = 'sha1';

    public function __construct(Array $params = [])
    {
        if (array_key_exists('date', $params)) {
            $this->date = new DateTime($params['date']);
        }
        if (array_key_exists('name', $params)) {
            $this->name = $params['name'];
        }
    }

    public function key()
    {
        return strtolower(str_replace(' ', '-', preg_replace('/[^0-9A-Za-z -]+/', '', $this->name) ?: ''));
    }

    public function getId()
    {
        return hash(self::$hashAlgorithm, $this->name);
    }
}
