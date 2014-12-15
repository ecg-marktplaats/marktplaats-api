.. index:: Autnentication, OAuth2

.. _authentication:

Authentication
==============

The Marktplaats API uses OAuth2 for authentication. For most usage, the *authorization_code*
grant type should be used. This is basically a 3-legged authentication flow. In this
guide we will give an example on how to do this authentication, which will result
in a valid *access_token* (and *refresh_token*) which can be used with the API.

Prerequisites
-------------

In order to succesfully authenticate against the Marktplaats API, you need to have your app
registered. In order to do this, contact Marktplaats and ask if you can get access to the API.
You will need to give a *redirect_uri* which is an endpoint on your side to handle redirects
from the Marktplaats authentication server. In return you will receive a *client_id* and
*client_secret*, which is needed in order to do the authentication with the Marktplaats API.

In this example we use the *client_id* ``myclientid`` and *client_secret* ``myclientsecret``.
The *redirect_uri* we use is ``http://localhost:3210/processredirect.php``

Step 1: obtaining authorization code
------------------------------------

In order to get the *access_token*, the first step is to do a call to the Marktplaats
Authorization server::

.. code-block:: http

  GET /accounts/oauth/authorize?response_type=code&client_id=myclientid&state=randomstate&redirect_uri=http%3A%2F%2Flocalhost%3A3210%2Fprocessredirect.php
  Host: auth.marktplaats.nl

In the PHP example we POST a HTML form to the ``authenticate.php`` with ``clientId``,
``clientSecret``, ``authorizationUrl``, ``accessTokenUrl`` and ``redirectUri`` as
parameters. ``authorizationUrl`` is the URL which is used to retrieve the *authorization_code*
while ``accessTokenUrl`` is the URL which is used to retrieve the *access_token*. These URL
for our production site are respectively ``https://auth.marktplaats.nl/accounts/oauth/authorize``
and ``https://auth.marktplaats.nl/accounts/oauth/token``

.. code-block:: PHP

  .. literalinclude:: ../phpexample/authenticate.php

After performing this step, the user will see a login page (in case he is not yet
logged in on the Marktplaats.nl website) and a confirmation page to grand access to
the client (app) which is requesting the authorization.

.. note::
  The optional parameter ``state`` can be used to store information which will later be
  returned by the authorization server on the redirect. If you don't need to transfer
  information, it is a good idea to still put a value in this parameter, in order to
  prevent Cross-site request forgery attempts.

.. image:: /images/accessconfirmation.png

Step 2: Retrieving the *access_token*
-------------------------------------

When the resource owner (user) grants access to your client to access Marktplaats.nl
on his behalf, the Marktplaats Authorization server will redirect the users' browser
to the provided *redirect_uri*, which is in our case ``http://localhost:3210/processredirect.php``
with the ``code`` as a parameter, which contains the *authorization_code* to be used to
retrieve the access_token. Our next task is to handle this redirect and retrieve
the *access_token* using the provided ``code``.

.. code-block:: PHP

  session_start();
  $authcode = $_GET['code'];
  $state = $_GET['state'];

This starts the PHP session, and stores the provided ``code`` in the variable
``$authcode`` as well as the ``state`` parameter.

.. code-block:: PHP

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

With the received *authorization_code* in the ``code`` parameter, and the data
which we stored in the session (which is the *client_id*, *client_secret*,
*access token url* and *redirect_uri*) we can construct the request to obtain an
*access_token*. For this, we construct the parameters for the request and do a
POST request to the *access token url*.

The POST request sent to the server looks like this:

.. code-block:: HTTP

  POST /accounts/oauth/token?grant_type=authorization_code&code=akAS72shjuqeah382&redirect_uri=http%3A%2F%2Flocalhost%3A3210%2Fprocessredirect.php&cliend_id=myclientid&client_secret=myclientsecret
  Host: auth.marktplaats.nl

The result of this POST request should be
a *200 OK*, and a JSON object in the body which looks like this:

.. code-block:: JSON

    {
      "access_token":"d79b4761-2268-4f03-a068-01eb26b3c7d2",
      "token_type":"Bearer",
      "expires_in":43199,
      "refresh_token":"d5bd2dcf-1219-4b65-aacc-86149ba55fb0",
      "scope":"read,write"
    }

You need to store the returned *access_token* and *refresh_token*, so you can reuse
it late for authentication and refreshing the *access_token* in case it is expired.

Step 3: Refreshing the *access_token*
-------------------------------------

When we receive the *access_token* we also receive a *refresh_token*. This can be
used to obtain a new *access_token*, which is required since the token will expire
after 24 hours. The *refresh_token* will not expire and as such is crucial in the
process of giving your users an unutrusive experience.

To get a new access token you need to do a HTTP POST request to the token endpoint.
This is very similar to obtaining the access token:

.. code-block:: PHP

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

The http request which is done looks like this:

.. code-block:: HTTP

  POST /accounts/oauth/token?grant_type=refresh_token&refresh_token=35f5fb15-9364-464a-854b-9ac0b344f108&redirect_uri=http%3A%2F%2Flocalhost%3A3210%2Fprocessredirect.php&cliend_id=myclientid&client_secret=myclientsecret
  Host: auth.marktplaats.nl

The returned JSON object has the same structure as the one returned when requesting
an *access_token*.
