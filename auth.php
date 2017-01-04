<?php

    $url = 'https://demo.memberclicks.net/services/auth';
    $data = 'apiKey=2406471784&username=demouser&password=demopass';

    // Get the curl session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    // Uncomment the following if you want JSON - the response is XML by default
//    $httpHeaders[] = "Accept: application/json";
//    curl_setopt($ch, CURLOPT_HEADER, true);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders );

    $result = curl_exec($ch);
    $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

    echo $result;

?>
