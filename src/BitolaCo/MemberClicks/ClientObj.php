<?php

namespace BitolaCo\MemberClicks;

use \ReflectionObject;
use \ReflectionProperty;

// ClientObj is a base class which contains a valid MemberClicks API client and is initialized by an array of attributes.
abstract class ClientObj
{
    protected $client;

    static $errNotInitialized = 'Not initialized';
    static $errNoId = 'No id';

    abstract protected function load(): string;

    public function __construct(MemberClicks &$apiClient, array $attrs = [])
    {
        $this->client = $apiClient;
        $this->copyAttrs($attrs);
    }

    public function setClient(MemberClicks &$apiClient)
    {
        $this->client = $apiClient;
    }

    protected function copyAttrs(array $attrs)
    {
        $reflect = new ReflectionObject($this);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($properties as $p) {
            $key = $p->getName();
            if (!array_key_exists($key, $attrs)) {
                continue;
            }
            $val = $attrs[$key];
            switch ($val) {
            case "true":
                $this->{$key} = true;
                break;
            case "false":
                $this->{$key} = false;
                break;
            default:
                $this->{$key} = $val;
            }
        }
    }

    public function toArray(): array {
        $data = [];
        $reflect = new ReflectionObject($this);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($properties as $p) {
            if ($p->isStatic()) {
                continue;
            }
            $key = $p->getName();
            $data[$key] = $this->{$key};
        }
        return $data;
    }

}
