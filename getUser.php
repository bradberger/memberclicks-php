<?php

    // Get the auth token.  If you've already made this call just use the same token from before.
    $url = 'https://demo.memberclicks.net/services/auth';
    $data = 'apiKey=2406471784&username=demouser&password=demopass';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $httpHeaders[] = "Accept: application/json";
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders );

    $result = curl_exec($ch);

    // Parse the json result
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($result, $header_size);

    $jsonResult = json_decode( $body );

    curl_close($ch);

    $token = $jsonResult->token;
    // end getting the token.  Sample code begins below


    // **** Get User ****
    $url = 'https://demo.memberclicks.net/services/user/21838877';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $httpHeaders[] = "Accept: application/json";
    $httpHeaders[] = "Authorization: ".$token;
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders );

    $result = curl_exec($ch);

    echo $result;

?>
