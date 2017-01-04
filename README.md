## MemberClicks API client for PHP

[![Build Status](https://semaphoreci.com/api/v1/brad/memberclicks-php/branches/master/shields_badge.svg)](https://semaphoreci.com/brad/memberclicks-php)
[![codecov](https://codecov.io/gh/bradberger/memberclicks-php/branch/master/graph/badge.svg)](https://codecov.io/gh/bradberger/memberclicks-php)

Work in progress, don't use it quite yet.

### Example usage


#### Initialize the client

```php

<?php

require_once __DIR__ . '/vendor/autoload.php';

use \BitolaCo\MemberClicks\MemberClicks;
use \BitolaCo\MemberClicks\User;
use \BitolaCo\MemberClicks\Event;

$apiKey = '2406471784';
$username = 'demouser';
$password = 'demopass';

$memberclicks = new MemberClicks($apiKey, $username, $password);
list(, $err) = $memberclicks->init();
if ($err) {
    http_response_code(500);
    echo sprintf('Error getting MemeberClicks auth token: %s', $err);
    return;
}
```

#### Get a single user

```php
// Get single user
$userID = '21838877';
list($user, $err) = $memberclicks->getUser($userID);
if ($err) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks user %s: %s', $userID, $err);
    return;
}

// Get single user, alternative method.
$userID = '21838877';
$user = new User($memberclicks);
$user->userId = $userID;
if ($err = $user->load()) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks user %s: %s', $userID, $err);
    return;
}

header('Content-Type: application/json');
echo json_encode($users);
```

#### Get a list of users

```php
// Get users.
list($users, $err) = $memberclicks->getUsers();
if ($err) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks users: %s', $err);
    return;
}

header('Content-Type: application/json');
echo json_encode($users);
```

#### Get a list of events

```php
// Get events.
list($events, $err) = $this->memberclicks->getEvents();
if ($err) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks events: %s', $err);
    return;
}

header('Content-Type: application/json');
echo json_encode($users);
```

#### Get a single event

```php
// Get event
list($event, $err) = $this->memberclicks->getEvent('344420');
if ($err) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks event: %s', $err);
    return;
}

header('Content-Type: application/json');
echo json_encode($event);
```

```php
// Get event, alternate method.
$event = new Event($this->memberclicks);
$event->eventId = '344420';
if ($err = $event->load()) {
    http_response_code(500);
    echo sprintf('Error getting MemberClicks event: %s', $err);
    return;
}

header('Content-Type: application/json');
echo json_encode($event);
```