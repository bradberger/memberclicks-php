<?php

namespace BitolaCo\MemberClicks;

class User extends ClientObj
{

    public $userId;
    public $groupId;
    public $orgId;
    public $contactName;
    public $userName;
    public $active;
    public $validated;
    public $deleted;
    public $formStatus;
    public $lastModify;
    public $noMassEmail;
    public $prefBBContact;
    public $prefBBImage;
    public $password;
    public $attributes;

    // TODO see https://help.memberclicks.com/hc/en-us/articles/230526207-User-Attributes
    public function getAttributes()
    {
        if (!$this->client) {
            return [null, 'Not initialized'];
        }
        return $this->client->getUserAttributes($this->userId);
    }

    public function setAttribute(UserAttribute $attr): array
    {
        return $this->client->setUserAttribute($attr);
    }

    // TODO see https://help.memberclicks.com/hc/en-us/articles/230526207-User-Attributes
    public function getAttribute(string $attributeID): array
    {
        if (!$this->client) {
            return [null, 'Not initialized'];
        }
        return $this->client->getUserAttribute($this->userId, $attributeID);
    }

    public function getAttributeByName(string $name): array {
        if (!$this->client) {
            return [null, 'Not initialized'];
        }
        return [null, 'Not implemented'];
    }

    public function load(): string
    {
        if (!$this->client) {
            return 'Not initialized';
        }
        if (!$this->userId) {
            return 'No user id';
        }
        list($user, $err) = $this->client->getUser($this->userId, ['includeAttrs' => 'true']);
        if ($err) {
            return $err;
        }
        $this->copyAttrs((array) $user);
        return '';
    }

    // TODO see https://help.memberclicks.com/hc/en-us/articles/230526187-User
    public function getPhoto()
    {
        return 'Not implemented';
    }

    public static function all(MemberClicks $api, array $params = []): array {
        return $api->getUsers($params);
    }
}
