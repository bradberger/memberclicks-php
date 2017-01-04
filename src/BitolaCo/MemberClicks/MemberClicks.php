<?php

namespace BitolaCo\MemberClicks;

use \GuzzleHttp\Client as HTTPClient;
use BitolaCo\MemberClicks\UserAttribute;

class MemberClicks {

    private $apiKey = '';
    private $username = '';
    private $password = '';
    private $token = '';
    private $endpoint = 'https://demo.memberclicks.net';

    public function __construct(string $apiKey, string $username, string $password)
    {
        $this->apiKey = $apiKey;
        $this->username = $username;
        $this->password = $password;
    }

    public function setEndpoint(string $url)
    {
        $this->endpoint = trim(rtrim($url, '/'));
    }

    public function init(): array {

        if ($this->token) {
            return [$this->token, ''];
        }

        $client = new HTTPClient();
        list($data, $err) = $this->do('POST', '/services/auth', [
            'apiKey' => $this->apiKey,
            'username' => $this->username,
            'password' => $this->password,
        ]);
        if ($err) {
            return [$data, $err];
        }

        if (is_object($data)) {
            $this->token = $data->token;
        }
        return [$this->token, ''];
    }

    public function getUser(string $userID, array $params = [])
    {
        list(, $err) = $this->init();
        if ($err) {
            return [null, $err];
        }

        list($data, $err) = $this->do('GET', sprintf('/services/user/%s', $userID));
        return [new User($this, (array) $data), $err];
    }

    public function getUsers(array $params = []): array
    {
        list(, $err) = $this->init();
        if ($err) {
            return [null, $err];
        }

        list($data, $err) = $this->do('GET', '/services/user', $params);
        if ($err) {
            return [null, $err];
        }

        $users = [];
        if (count($data->user)) {
            foreach($data->user as $k => $u) {
                $users[] = new User($this, (array) $u);
            }
        }
        return [$users, ''];
    }

    public function getUserAttributes(string $userID): array
    {
        list(, $err) = $this->init();
        if ($err) {
            return [null, $err];
        }

        list($data, $err) = $this->do('GET', sprintf('/services/user/%s/attribute', $userID));
        if ($err) {
            return [null, $err];
        }
        if ($data === null) {
            $data = [];
        }
        return [$data, ''];
    }

    public function getUserAttribute(string $userID, string $attributeID)
    {
        list(, $err) = $this->init();
        if ($err) {
            return [null, $err];
        }

        list($data, $err) = $this->do('get', sprintf('/services/user/%s/attribute/%s', $userID, $attributeID));
        if ($err) {
            return [null, $err];
        }

        return [new UserAttribute($this, (array) $data), ''];

    }

    public function getOrganizationName(): array
    {
        list($data, $err) = $this->do('GET', '/services/org/name');
        return [$data, $err];
    }

    public function getEvent($eventID): array
    {
        list($data, $err) = $this->do('GET', sprintf('/services/event/%s', $eventID));
        if ($err) {
            return [null, $err];
        }
        return [new Event($this, (array) $data), ''];
    }

    public function getEvents(array $params = []): array
    {
        list($data, $err) = $this->do('GET', '/services/event');
        if ($err) {
            return [null, $err];
        }
        if (empty($data)) {
            return [[], null];
        }
        $events = [];
        foreach($data->eventList as $event) {
            $events[] = new Event($this, (array) $event);
        }
        return [$events, ''];
    }

    public function getGroup($groupID): array
    {
        list($data, $err) = $this->do('GET', sprintf('/services/group/%s', $groupID));
        if ($err) {
            return [null, $err];
        }
        return [new Group($this, (array) $data), ''];
    }

    public function getGroups(array $params = []): array
    {
        list($data, $err) = $this->do('GET', '/services/group');
        if ($err) {
            return [null, $err];
        }
        if (empty($data)) {
            return [[], null];
        }
        $groups = [];
        // print_r($data);
        foreach($data->group as $group) {
            $groups[] = new Group($this, (array) $group);
        }
        return [$groups, ''];
    }

    public function setUserAttribute(UserAttribute $attr): array
    {
        list($data, $err) = $this->do('PUT', sprintf('/services/user/%s/attribute/%s', $attr->userId, $attr->attId), $attr->toArray());
        return [$data, $err];
    }

    private function do(string $method, string $url, array $params = [], array $headers = []): array
    {
        try {
            $method = strtoupper($method) ?: 'GET';
            $requestParams = ['headers' => ['Accept' => 'application/json']];
            if ($this->token) {
                $requestParams['headers']['Authorization'] = $this->token;
            }
            if (($method === 'POST') && count($params)) {
                $requestParams['form_params'] = $params;
            } else if ($method === 'GET' && count($params)) {
                $requestParams['query'] = $params;
            } else if ($method === 'PUT' && count($params)) {
                $requestParams['json'] = $params;
            }

            $client = new HTTPClient();
            $res = $client->request($method, $this->makeURL($url), $requestParams);

            switch ((int) $res->getStatusCode()) {
            case 204:
            case 200:
                $data = json_decode($res->getBody()->getContents());
                return [$data, ''];
            default:
                return [null, $res->getBody()->getContents()];
            }
        } catch(\Exception $e) {
            return [null, $e->getMessage()];
        }
    }

    private function makeURL(string $url): string
    {
        return sprintf('%s/%s', trim(rtrim($this->endpoint, '/')), trim(ltrim($url, '/')));
    }
}
