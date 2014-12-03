<?php
session_start();

$refreshToken = $_SESSION['refresh_token'];
$accessTokenUrl = $_SESSION['accessTokenUrl'];
$clientId = $_SESSION['clientId'];
$clientSecret = $_SESSION['clientSecret'];

$params = "grant_type=refresh_token&refresh_token=$refreshToken&client_id=$clientId&client_secret=$clientSecret";

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/json",
        'timeout' => 60
    )
);

$context = stream_context_create($opts);
$result = file_get_contents($accessTokenUrl . "?$params", false, $context, -1, 40000);
$jsonresult = json_decode($result);
$_SESSION['access_token'] = $jsonresult->access_token;
$_SESSION['refresh_token'] = $jsonresult->refresh_token;

header("Location: index.php");