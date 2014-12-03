<?php
session_start();
$clientId = $_POST['clientId'];
$clientSecret = $_POST['clientSecret'];
$authorizationUrl = $_POST['authorizationUrl'];
$accessTokenUrl = $_POST['accessTokenUrl'];
$redirectUri = urlencode($_POST['redirectUri']);

$url = $authorizationUrl . "?response_type=code&client_id=$clientId&state=randomstate&redirect_uri=$redirectUri";

$_SESSION['clientId'] = $clientId;
$_SESSION['clientSecret'] = $clientSecret;
$_SESSION['accessTokenUrl'] = $accessTokenUrl;
$_SESSION['redirectUri'] = $redirectUri;

//echo $url;
header("Location: $url");
