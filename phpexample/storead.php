<?php
include_once('includes/utils.php');

session_start();

$apiUrl = 'https://api.marktplaats.dev';
$accessToken = $_SESSION['access_token'];

$l1id=$_GET['l1id'];
$categoryId = $_POST['categoryId'];

$adData = $_POST;
$fixedPrice = $_POST['fixedPrice'];
unset($adData['fixedPrice']);
$adData['priceModel']['modelType'] = 'fixed';
$adData['priceModel']['askingPrice'] = intval($fixedPrice);
$adData['location']['postcode'] = $adData['postcode'];
unset($adData['postcode']);
$adData['categoryId'] = intval($adData['categoryId']);

$attributes = retrieveAttributes($apiUrl, $l1id, $categoryId, $accessToken);

foreach($attributes->fields as $attribute) {
    if ($attribute->type == 'NUMERIC' && isset($adData[$attribute->key])) {
        $adData[$attribute->key] = intval($adData[$attribute->key]);
    }
}

$adJson = json_encode($adData);

$url = $apiUrl . "/v1/advertisements?_links=false";

$jsonresult = apiCall($url, $adJson, $accessToken, 'POST');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marktplaats API Place advertisement</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<h1>Marktplaats API Place advertisement</h1>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">Advertisement</div>
        <div class="panel-body">
            <dl class="dl-horizontal">
                <?php foreach ($jsonresult as $key => $value) {
                    if ($key != '_embedded') {
                        if (gettype($value) == 'object') {
                            echo "<dl class='dl-horizontal'>";
                            foreach ($value as $x => $y) {
                                ?>
                                <dt><?= $x ?></dt>
                                <dd><?= $y ?></dd>
                            <?php
                            }
                            echo "</dl>";
                        } else {
                            ?>
                            <dt><?= $key ?></dt>
                            <dd><?= $value ?></dd>
                            <?php
                        }
                    }
                }?>
            </dl>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>