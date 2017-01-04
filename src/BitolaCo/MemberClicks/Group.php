<?php

namespace BitolaCo\MemberClicks;

class Group extends ClientObj
{

    public $groupType;
    public $groupID;
    public $orgId;
    public $groupName;
    public $ghostUser;
    public $specialGroup;
    public $bypassFormLogin;

    public function load(): string
    {
        if (!$this->client) {
            return 'Not initialized';
        }
        if (!$this->groupID) {
            return 'No group id';
        }
        list($group, $err) = $this->client->getGroup($this->groupID);
        if ($err) {
            return $err;
        }
        $this->copyAttrs((array) $group);
        return '';
    }

    public function all(): array
    {
        if (!$this->client) {
            return 'Not initialized';
        }
        return $this->client->getGroups();
    }
}
