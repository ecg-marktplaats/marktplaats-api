<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marktplaats API PHP example</title>

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
<h1>Marktplaats API PHP example</h1>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">Authentication Example</div>
        <div class="panel-body">
            <p>This example with go to the authentication page and get an accesstoken. In order to authenticate, use <strong>username</strong> <code>10@marktplaats.qa</code> and <strong>password</strong> <code>test123</code></p>
            <form role="form" method="POST" action="authenticate.php">
                <div class="form-group">
                    <label for="authorizationUrl">authorizationUrl</label>
                    <input type="text" class="form-control" id="authorizationUrl" name="authorizationUrl" placeholder="https://auth.demo.qa-mp.so/accounts/oauth/authorize" value="https://auth.demo.qa-mp.so/accounts/oauth/authorize">
                </div>
                <div class="form-group">
                    <label for="accessTokenUrl">accessTokenUrl</label>
                    <input type="text" class="form-control" id="accessTokenUrl" name="accessTokenUrl" placeholder="https://auth.demo.qa-mp.so/accounts/oauth/token" value="https://auth.demo.qa-mp.so/accounts/oauth/token">
                </div>
                <div class="form-group">
                    <label for="redirectUri">redirectUri</label>
                    <input type="text" class="form-control" id="redirectUri" name="redirectUri" placeholder="http://localhost:8888/processredirect.php" value="http://<?= $_SERVER['HTTP_HOST'] . '/processredirect.php'; ?>">
                </div>
                <div class="form-group">
                    <label for="clientId">client_id</label>
                    <input type="text" class="form-control" id="clientId" name="clientId" placeholder="client_id" value="phpexample">
                </div>
                <div class="form-group">
                    <label for="clientSecret">client_secret</label>
                    <input type="text" class="form-control" id="clientSecret" name="clientSecret" placeholder="client_secret" value="ySZy8YAMKBm62UNFE3W7ovJUYpKUKl">
                </div>

                <button type="submit" class="btn btn-default">Get Accesstoken</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Access Token operations</div>
        <div class="panel-body">
            <form role="form" method="POST" action="refreshtoken.php">
                <div class="form-group">
                    <label for="accessToken">Access Token (from session)</label>
                    <input type="text" class="form-control accesstoken" id="accessToken" name="accessToken" placeholder="No access token on session" value="<?= $_SESSION['access_token'];?>">
                </div>
                <div class="form-group">
                    <label for="refreshToken">Refresh Token (from session)</label>
                    <input type="text" class="form-control" id="refreshToken" name="refreshToken" placeholder="No refresh token on session" value="<?= $_SESSION['refresh_token'];?>">
                </div>
                <button type="submit" class="btn btn-default">Refresh Accesstoken</button>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Place advertisement</div>
        <div class="panel-body">
            <form role="form" method="POST" action="refreshtoken.php">
                <div class="dropdown">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" class="btn btn-default">
                        Select category
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu l1cats" role="menu" aria-labelledby="dLabel">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">item1</a></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#">item2</a></li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script>
    function initCategories() {
        var accesstoken = $(".accesstoken").val();
        if (accesstoken !== "") {
            $.get("categories.php?access_token=" + accesstoken)
                .done(function(result) {
                    var $l1cats = $('.l1cats');
                    $l1cats.empty();
                    var l1cats = $.parseJSON(result);
                    for (var i = 0; i < l1cats.length; i++) {
                        $l1cats.append("<li role='presentation'><a role='menuitem' tabindex='-1' href='#' value='" + l1cats[i].id + "'>" + l1cats[i].name + "</a></li>");
                    }
                });

        } else {
            alert('undefined' + typeof(accesstoken));
        }
    }

    $(document).ready(initCategories)

</script>
</body>
</html>