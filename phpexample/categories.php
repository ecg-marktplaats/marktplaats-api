<?php
include_once('includes/utils.php');
session_start();

$apiUrl = 'https://api.marktplaats.dev';
$l1id = $_GET['l1id'];
$l1name = $_GET['l1name'];
$l2id = $_GET['l2id'];

$accesstoken = $_SESSION['access_token'];
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

$jsonresult = apiCall($url, '', $accesstoken);

foreach($jsonresult->_embedded as $jsoncats) {
    foreach($jsoncats as $jsoncat) {
        $cat['name'] = $jsoncat->name;
        $cat['id'] = $jsoncat->categoryId;
        $cats[] = $cat;
    }
}

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
        <div class="panel-heading">Select category</div>
        <div class="panel-body">
            <?php if (strlen($l1name) > 0) { ?>
                <h2><?= $l1name ?></h2>
            <?php } ?>
            <ul class="list-group">
                <?php
                    foreach($cats as $cat) {
                        if (strlen($l1id) > 0) {
                            echo "<li class='list-group-item'><a href='createad.php?l2id={$cat['id']}&l1id={$l1id}&l2name={$cat['name']}'>{$cat['name']}</a></li>";
                        } else {
                            echo "<li class='list-group-item'><a href='categories.php?l1id={$cat['id']}&l1name={$cat['name']}'>{$cat['name']}</a></li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>