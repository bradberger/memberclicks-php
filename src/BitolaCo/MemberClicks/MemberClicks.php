<?php

namespace BitolaCo\MemberClicks;

use \GuzzleHttp\Client as HTTPClient;
use BitolaCo\MemberClicks\UserAttribute;

class MemberClicks {

    private $clientID, $clientSecret, $orgID;
    private $token = '';

    public function __construct(string $orgID, $clientID, $clientSecret)
    {
        $this->orgID = $orgID;
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
    }

    private function do(string $method, string $url, array $params = [], array $headers = []): array
    {
        try {
            $method = strtoupper($method) ?: 'GET';
            $requestParams = ['headers' => ['Accept' => 'application/json']+$headers];
            if ($this->token && $this->token->accessToken && !array_key_exists('Authorization', $headers)) {
                $requestParams['headers']['Authorization'] = 'Bearer '.$this->token->accessToken;
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

    // auth gets a Client Credentials Grant Type auth token. It returns an AccessToken and error
    public function auth(string $scope = 'read'): array
    {
        list($data, $err) = $this->do(
            'POST',
            '/oauth/v1/token',
            ['grant_type' => 'client_credentials', 'scope' => 'read'],
            ['Authorization' => 'Basic '.base64_encode(sprintf('%s:%s', $this->clientID, $this->clientSecret))]
        );
        if ($err) {
            return [null, $err];
        }
        $this->token = new AccessToken((array) $data);
        return [$this->token, null];
    }

    public function resourceOwnerToken(string $username, string $password, string $scope = 'read'): array
    {
        list($data, $err) = $this->do('POST', '/api/v1/token', [
            'grant_type' => 'refresh_token',
            'scope' => $scope,
            'username' => $username,
            'password' => $password,
        ], ['Authorization' => $this->getBasicAuthStr()]);
        if ($err) {
            return [null, $err];
        }
        return [new Token((array) $data), null];
    }

    public function checkLogin(string $username, string $password): boolean
    {
        list(, $err) = $this->resourceOwnerToken($username, $password);
        return empty($err);
    }

    public function events(): array
    {
        list($data, $err) = $this->do('GET', '/api/v1/event');
        if ($err) {
            return [null, $err];
        }
        return [array_map(function($event) {
            return new Event((array) $event);
        }, $data->events), null];
    }

    private function getBasicAuthStr()
    {
        return 'Basic '.base64_encode(sprintf('%s:%s', $this->clientID, $this->clientSecret));
    }

    private function makeURL(string $url): string
    {
        return sprintf('https://%s.memberclicks.net/%s', $this->orgID, trim(ltrim($url, '/')));
    }
}
