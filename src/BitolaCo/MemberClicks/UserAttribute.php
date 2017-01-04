<?php

namespace BitolaCo\MemberClicks;

class UserAttribute extends ClientObj
{
    public $userId;
    public $attId;
    public $attName;
    public $attTypeId;
    public $attTypeName;
    public $attData;

    public function load(): string
    {
        if (!$this->client) {
            return self::errNotInitialized;
        }
        if (!$this->userId || !$this->attId) {
            return self::$errNoId;
        }
        list($attr, $err) = $this->client->getUserAttribute($this->userId, $this->attId);
        if ($err) {
            return $err;
        }
        $this->copyAttrs((array) $attr);
        return '';
    }
}
