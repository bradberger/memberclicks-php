<?php

require_once __DIR__ . '/vendor/autoload.php';

use \BitolaCo\MemberClicks\MemberClicks;
use \BitolaCo\MemberClicks\User;
use Dotenv\Dotenv;

$apiKey = '2406471784';
$username = 'demouser';
$password = 'demopass';

if (file_exists(__DIR__.'/.env')) {
    $dotenv = new Dotenv(__DIR__);
    $dotenv->load();
}

$memberclicks = new MemberClicks(getenv('MEMBERCLICKS_ORG_ID'), getenv('MEMBERCLICKS_CLIENT_ID'), getenv('MEMBERCLICKS_CLIENT_SECRET'));
$memberclicks->auth();

$baseURL = sprintf(
    '%s://%s:%s/example.php',
    strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'] ?? "localhost",
    $_SERVER['SERVER_PORT']
);

// Login link.
if (true || array_key_exists('code', $_GET)) {
    echo sprintf('<a href="%s">Login</a><br>', $memberclicks-> requestAuthCode($baseURL, 'read', 'test', false));
}

list($memberTypes, $err) = $memberclicks->memberTypes('Individual');
if ($err) {
    echo sprintf('Could not get member types: %s<br>', $err);
    exit(1);
}

list($events, $err) = $memberclicks->futureEvents();
if ($err) {
    echo sprintf('Error getting events: %v', $err);
    exit(1);
}

echo '<form method="POST">';
foreach($events as $event) {
    echo sprintf('<div><h4>%s</h4>', $event->name);
    echo sprintf('<input type="text" name="events[%s][currency]" placeholder="Currency">', $event->key());
    foreach($memberTypes as $memberType) {
        echo sprintf('<input type="text" name="events[%s][pricing][%s]" value="" placeholder="Pricing for %s members">', $event->key(), $memberType->name, $memberType->name);
    }
    echo '</div>';
}
echo '<button type="submit">Save</button>';
echo '</form>';

if (!empty($_POST['events'])) {
    foreach($_POST['events'] as $key => $event) {
        echo "<pre>";
        echo sprintf("REPLACE INTO event_prices (id, currency, pricing) VALUES('%s', '%s', '%s')", $key, $event['currency'], json_encode($event['pricing']));
        echo "</pre>";
    }
    var_export($_POST);
}

if (array_key_exists('code', $_GET)) {

    $authCode = $_GET['code'];
    list($token, $err) = $memberclicks->getTokenFromAuthCode($authCode, $baseURL, 'read', 'test');
    if ($err) {
        echo sprintf('Authentication error: %s', $err);
        exit(1);
    }

    list($profile, $err) = $memberclicks->getUserFromToken($token);
    if ($err) {
        echo sprintf('Error getting user: %s<br>', $err);
        exit(1);
    }

    echo sprintf('Welcome back, %s!<br>', $profile->name_first);
    echo sprintf('<input type="text" name="profile_name" value="%s" readonly><br>', $profile->profile_id);
    echo sprintf('<input type="text" name="profile_id" value="%s" readonly><br>', $profile->contact_name);
    foreach ($events as $event) {
        echo sprintf('<input type="radio" name="event" value="%s"> %s (%s)<br>', $event->name, $event->name, $event->date->format('Y-m-d'));
    }
}
