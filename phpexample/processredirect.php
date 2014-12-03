<?php
session_start();
$authcode = $_GET['code'];
$state = $_GET['state'];

$clientId = $_SESSION['clientId'];
$clientSecret = $_SESSION['clientSecret'];
$accessTokenUrl = $_SESSION['accessTokenUrl'];
$redirectUri = $_SESSION['redirectUri'];


$params = "grant_type=authorization_code&code=$authcode&redirect_uri=$redirectUri&client_id=$clientId&client_secret=$clientSecret";

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/json",
        'timeout' => 60
    )
);

//echo $params;

$context = stream_context_create($opts);
$result = file_get_contents($accessTokenUrl . "?$params", false, $context, -1, 40000);
$jsonresult = json_decode($result);
$_SESSION['access_token'] = $jsonresult->access_token;
$_SESSION['refresh_token'] = $jsonresult->refresh_token;

header("Location: index.php");