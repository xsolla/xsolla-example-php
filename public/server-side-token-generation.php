<?php

// Usually you keep your API key, projectId, and generate body in your server, not on client. But just for example we get it from client
$project = $_POST['project'];
$apiKey = $_POST['apikey'];
$body = $_POST['body'];

$bodyJson = json_decode($body, true);
if ($bodyJson === null) {
    echo 'Json in body is not valid';
    return;
}

// Prepare the CURL request
$url = "https://store.xsolla.com/api/v3/project/{$project}/admin/payment/token";
$ch = curl_init($url);

$authorization = 'Basic ' . base64_encode("{$project}:{$apiKey}");
$headers = [
    'Content-Type: application/json',
    'Authorization: ' . $authorization
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the CURL request
$response = curl_exec($ch);
curl_close($ch);

// Echo the response
echo $response;
