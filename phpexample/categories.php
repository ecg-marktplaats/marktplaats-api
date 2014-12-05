<?php
session_start();

$apiUrl = 'https://api.demo.qa-mp.so';
$l1id = $_GET['l1id'];
$l2id = $_GET['l2id'];

$accesstoken = $_GET['access_token'];
/**
 * Retrieve categories.
 */
$opts = array('http' =>
    array(
        'method'  => 'GET',
        'header'  => "Content-Type: application/json\r\nAuthorization: Bearer $accesstoken",
        'timeout' => 60
    )
);

$url = $apiUrl . "/v1/categories";
if (strlen($l1id) > 0) {
    $url .= "/$l1id";
}

if (strlen($l2id) > 0) {
    $url .= "/$l2id";
}

$context = stream_context_create($opts);
$result = file_get_contents($url, false, $context, -1);
$jsonresult = json_decode($result);


foreach($jsonresult->_embedded as $jsoncats) {
    foreach($jsoncats as $jsoncat) {
        $cat['name'] = $jsoncat->name;
        $cat['id'] = $jsoncat->categoryId;
        $cats[] = $cat;
    }
}
echo json_encode($cats);