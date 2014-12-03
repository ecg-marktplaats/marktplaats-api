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
  GET /accounts/oauth/authorize?response_type=code&client_id=myclientid&state=randomstate&redirect_uri=http://localhost:3210/processredirect.php
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

.. INFO::
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
