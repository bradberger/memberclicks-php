<?php

namespace BitolaCo\MemberClicks;

use \GuzzleHttp\Client as HTTPClient;
use BitolaCo\MemberClicks\UserAttribute;

class MemberClicks {

    private $clientID, $clientSecret, $orgID;
    public $token = '';

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

    // requestAuthCode implements the first step of the "Authorization Code grant type"
    // @see https://help.memberclicks.com/hc/en-us/articles/230536287-API-Authorization
    // It returns a properly formatted link for redirection
    public function requestAuthCode(string $redirectURI = '', string $scope = 'read', string $state = '', bool $redirect = false): string
    {
        $url = sprintf(
            'https://%s.memberclicks.net/oauth/v1/authorize?response_type=code&client_id=%s&scope=%s&state=%s&redirect_uri=%s',
            $this->orgID,
            $this->clientID,
            $scope,
            $state,
            $redirectURI
        );

        if ($redirect) {
            header(sprintf('Location: %s', $url));
        }

        return $url;
    }

    // parseAuthCode implements the second step of the "Authorization Code grant type"
    // @see https://help.memberclicks.com/hc/en-us/articles/230536287-API-Authorization
    public function getTokenFromAuthCode(string $authCode, string $redirectURI = '', string $scope = 'read', string $state = ''): array
    {
        list($data, $err) = $this->do('POST', '/oauth/v1/token', [
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'scope' => $scope,
            'redirect_uri' => $redirectURI
        ], ['Authorization' => $this->getBasicAuthStr()]);
        if ($err) {
            return [null, $err];
        }

        return [new AccessToken((array) $data), null];
    }

    public function getUserFromToken(AccessToken $token): array
    {
        list($data, $err) = $this->do('GET', '/api/v1/profile/me', [], [
            'Authorization' => 'Bearer '.$token->access_token
        ]);
        if ($err) {
            return [null, $err];
        }
        return [new Profile((array) $data), null];
    }

    public function profile(string $profileID): array
    {
        list($data, $err) = $this->do('GET', '/api/v1/profile/'.$profileID);
        if ($err) {
            return [null, $err];
        }
        return [new Profile((array) $data), null];
    }

    public function resourceOwnerToken(string $username, string $password, string $scope = 'read'): array
    {
        list($data, $err) = $this->do('POST', '/oauth/v1/token', [
            'grant_type' => 'password',
            'scope' => $scope,
            'username' => $username,
            'password' => $password,
        ], ['Authorization' => $this->getBasicAuthStr()]);
        if ($err) {
            return [null, $err];
        }
        return [new AccessToken((array) $data), null];
    }

    public function checkLogin(string $username, string $password): bool
    {
        list(, $err) = $this->resourceOwnerToken($username, $password);
        return empty($err);
    }

    // me returns the profile for the user with the given username/password combination
    public function me(string $username, string $password): array
    {
        list($token, $err) = $this->resourceOwnerToken($username, $password);
        if ($err) {
            return [null, $err];
        }
        return $this->getUserFromToken($token);
    }

    public function memberTypes(string $typeFilter=''): array
    {
        list($data, $err) = $this->do('GET', '/api/v1/member-type');
        if ($err) {
            return [null, $err];
        }

        $types = array_map(function($type) {
            return new MemberType((array) $type);
        }, $data->memberTypes);

        if ($typeFilter) {
            $typeFilter = strtolower($typeFilter);
            $types = array_filter($types, function($type) use ($typeFilter) {
                return strtolower($type->type) === $typeFilter;
            });
        }

        return [$types, null];
    }

    public function events(bool $desc = false): array
    {
        list($data, $err) = $this->do('GET', '/api/v1/event');
        if ($err) {
            return [null, $err];
        }

        $events = array_map(function($event) {
            return new Event((array) $event);
        }, $data->events);

        // Sort events by date, with oldest first.
        usort($events, function($a, $b) use($desc) {
            if ($desc) {
                return $a->date->getTimestamp() < $b->date->getTimestamp();
            }
            return $a->date->getTimestamp() > $b->date->getTimestamp();
        });

        return [$events, null];
    }

    // futureEvents returns all events which are in the future.
    public function futureEvents(bool $desc = false)
    {
        list($events, $err) = $this->events($desc);
        if ($err) {
            return [null, $err];
        }
        $now = time();
        return [array_filter($events, function($event) use ($now) {
            return $event->date->getTimestamp() >= $now;
        }), null];
    }

    // pastEvents returns all events which are in the past
    public function pastEvents(bool $desc = false)
    {
        list($events, $err) = $this->events($desc);
        if ($err) {
            return [null, $err];
        }
        $now = time();
        return [array_filter($events, function($event) use ($now) {
            return $event->date->getTimestamp() < $now;
        }), null];
    }

    // formatProfiles takes a array of stdObject profiles and returns an array of Profile objects.
    // If the memberType and onlyActive are set it will filter based on those parameters as well.
    private function formatProfiles(Array $profiles, $memberType = '', $onlyActive = false): array {
        return array_values(array_filter(array_map(function($profile) {
            return new Profile((array) $profile);
        }, $profiles), function($profile) use ($memberType, $onlyActive) {
            if ($memberType && $profile->member_type != $memberType) {
                return false;
            }
            if ($onlyActive && $profile->member_status !== 'Active') {
                return false;
            }
            return true;
        }));
    }

    // profileCount returns the total number of profiles. Useful for pagination, etc.
    public function profileCount()
    {
        list($data, $err) = $this->do('GET', '/api/v1/profile?pageNumber='.$start);
        if ($err) {
            return [null, $err];
        }
        return [$data->totalPageCount, null];
    }

    // Profiles returns all profiles which match the criteria. Each page (per memberclicks) is 10
    // profiles, so take that into account when using start and limit.
    public function profiles($memberType = '', $onlyActive = true, int $start = 1, int $limit = 5)
    {
        // Get first set of profiles to determine number of pages.
        list($data, $err) = $this->do('GET', '/api/v1/profile?pageNumber='.$start);
        if ($err) {
            return [null, $err];
        }
        $profiles = $this->formatProfiles($data->profiles);

        // If there are more pages then the page we started on, then continue getting
        // them up to the limit of pages allowed.
        if ($data->totalPageCount > $start) {

            // Number of pages to get. If no limit, do the total number of pages.
            // If there is a limit, do the smaller amount between the total number
            // of pages and the limit.
            $pages = $limit ? min($start+$limit, $data->totalPageCount+1) : $data->totalPageCount+1;
            for ($i = $start+1; $i < $pages; $i++) {
                list($data, $err) = $this->do('GET', '/api/v1/profile?pageNumber='.$i);
                if ($err) {
                    return [$profiles, $err];
                }
                foreach($this->formatProfiles($data->profiles) as $profile) {
                    array_push($profiles, $profile);
                }
            }
        }

        return [$profiles, null];
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
