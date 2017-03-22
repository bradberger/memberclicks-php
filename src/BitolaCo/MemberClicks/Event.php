<?php

namespace BitolaCo\MemberClicks;

use DateTime;

class Event
{
    public $name;
    public $date;
    public function __construct(Array $params = [])
    {
        if (array_key_exists('date', $params)) {
            $this->date = new DateTime($params['date']);
        }
        if (array_key_exists('name', $params)) {
            $this->name = $params['name'];
        }
    }
}
