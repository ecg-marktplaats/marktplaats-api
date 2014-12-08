<?php
include_once('includes/utils.php');
session_start();
$apiUrl = 'https://api.marktplaats.dev';
$l2name = $_GET['l2name'];
$l1id = $_GET['l1id'];
$l2id = $_GET['l2id'];

$accessToken = $_SESSION['access_token'];

function renderAttributes($apiUrl, $accessToken, $l1id, $l2id) {
    $jsonresult = retrieveAttributes($apiUrl, $l1id, $l2id, $accessToken);
    foreach ($jsonresult->fields as $attribute) {
        renderAttribute($attribute);
    }
}

function renderAttribute($attribute) {
    if ($attribute->writable) {
        if ($attribute->type == 'STRING') {
            renderStringAttribute($attribute);
        } else if ($attribute->type == 'LIST') {
            renderListAttribute($attribute);
        } else {
            renderNumberAttribute($attribute);
        }
    }
}

function renderStringAttribute($attribute) {
    ?>
    <div class="form-group">
        <label for="<?= $attribute->key ?>"><?=$attribute->label?></label>
        <?php if (count($attribute->values) > 0) { ?>
            <select class="form-control" id="<?= $attribute->key ?>" name="<?= $attribute->key ?>">
            <?php foreach ($attribute->values as $value) { ?>
                <option value="<?= $value?>"><?= $value ?></option>
            <?php } ?>
            </select>
        <?php } else { ?>
            <input type="text" class="form-control" id="<?= $attribute->key ?>" name="<?= $attribute->key ?>">
        <?php } ?>
    </div>
    <?php
}

function renderListAttribute($attribute) {
    ?>
    <div class="form-group">
        <label for="<?= $attribute->key ?>"><?=$attribute->label?></label>
        <?php foreach ($attribute->values as $value) { ?>
            <label class="checkbox-inline">
                <input type="checkbox" id="<?= $attribute->key ?>" name="<?= $attribute->key ?>" value="<?= $value ?>"> <?= $value ?>
            </label>
        <?php } ?>
    </div>
    <?php
}

function renderNumberAttribute($attribute) {
    ?>
    <div class="form-group">
        <label for="<?= $attribute->key ?>"><?= $attribute->label ?></label>
        <input type="text" class="form-control" id="<?= $attribute->key ?>" name="<?= $attribute->key ?>">
    </div>
    <?php
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
        <div class="panel-heading">Create advertisement</div>
        <div class="panel-body">
            <form role="form" method="POST" action="storead.php?l1id=<?=$l1id?>">
                <input type="hidden" id="categoryId" name="categoryId" value="<?= $l2id ?>">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="title">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" class="form-control" id="description" name="description" placeholder="description">
                </div>
                <div class="form-group">
                    <label for="fixedPrice">Price</label>
                    <input type="text" class="form-control" id="fixedPrice" name="fixedPrice" placeholder="0,00">
                </div>
                <div class="form-group">
                    <label for="postcode">Postcode</label>
                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="postcode">
                </div>
                <?php renderAttributes($apiUrl, $accessToken, $l1id, $l2id); ?>
                <button type="submit" class="btn btn-default">Place advertisement</button>
            </form>
        </div>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>