## MemberClicks API client for PHP

Work in progress, don't use it quite yet.

### Example usage

```php

<?php

require_once __DIR__ . '/vendor/autoload.php';

use \BitolaCo\MemberClicks\MemberClicks;
use \BitolaCo\MemberClicks\User;

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
