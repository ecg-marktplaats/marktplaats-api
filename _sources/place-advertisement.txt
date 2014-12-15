.. index:: Advertisement

.. _place_advertisement:

Place Advertisement
===================

Placing an advertisement using the Marktplaats API is at the moment only possible
in free categories, unless you have a contract with Marktplaats, in which case
advertisements are payed using an invoice. This example will show how to place an
advertisement in a free category including images.

In this example we use the *access_token* ``myaccesstoken`` to illustrate that
in that place the *access_token* should be filled in.

The full PHP example of which snippets are used in this guide can be found in this
repository in the directory ``/phpexample``.

Prerequisites
-------------

You need to have an *access_token* which can be used to authenticate the request
to the API. See :ref:`authentication` on how to get such a token.


Step 1: Get the attributes for an category
------------------------------------------

Before we can place an advertisement, we need to now which in which category we
will place the advertisement and which *attributes* we can use. *Attributes* are
nothing more than *fields* in the JSON, but which are specific for the category
the advertisement is placed in. For example, in a cars category, you can specify
the *constructionYear* or the *model*, but these fields are not available in the
books category. Some categories have mandatory fields. These fields have to be
provided when creating a new advertisement. For example, in the cd players category,
the condition attribute is mandatory.

To get all the categories on Marktplaats we do a GET request on the endpoint
/v1/categories:

.. code-block:: HTTP

  GET /v1/categories
  Host: api.marktplaats.nl
  Authorization: Bearer myaccesstoken
  Content-Type: application/json

The response is a JSON object with all top level categories on Marktplaats:

.. code-block:: JSON

  {
    "_links": {
      "self": {
        "href": "/v1/categories"
      },
      "find": {
        "href": "/v1/categories{?categoryId}",
        "templated": true
      },
      "describedby": {
        "href": "https://api.demo.qa-mp.so/docs/v1/categories.html"
      },
      "curies": [
        {
          "href": "https://api.demo.qa-mp.so/docs/v1/rels/{rel}.html",
          "templated": true,
          "name": "mp"
        }
      ]
    },
    "_embedded": {
      "mp:category": [
        {
          "_links": {
            "self": {
              "href": "/v1/categories/1"
            },
            "up": {
              "href": "/v1/categories"
            }
          },
        "_embedded": {
          "mp:category": [
            {
              "_links": {
              "self": {
              "href": "/v1/categories/1/2"
              },
              "up": {
              "href": "/v1/categories/1"
              },
                "mp:category-attributes": {
                "href": "/v1/categories/1/2/attributes"
              }
              },
                "categoryId": 2,
                "name": "Antiek | Bestek"
              },
            ...
            }
          ]
        },
        ...
      ]
    }
  }

The result above is abbreviated, but the idea is clear.

After we have determined which category we want to place the advertisement in,
we can get the attributes for that category and use those to create the advertisement.
For example, if we want to place an advertisement in the cd player category, we can
get the attributes for that category by doing a GET request to ``/v1/categories/31/35/attributes``

.. code-block:: HTTP

  GET /v1/categories/31/35/attributes
  Host: api.marktplaats.nl
  Authorization: Bearer myaccesstoken
  Content-Type: application/json

The result will be a list of attributes, where each attribute has the following
structure:

.. code-block:: JSON

  {
    "key": "properties",
    "label": "Eigenschappen",
    "type": "LIST",
    "values": [
      "Wisselaar",
      "Draagbaar",
      "Met radio"
    ],
    "mandatory": false,
    "searchable": true,
    "writable": true,
    "updateable": true
  },


This attribute *properties* will be presented as *Eigenschappen* on the website,
and it only accepts the values listed in the *values* array. If a different value
is specified, an error will be produced. Furthermore, the attribute is not mandatory,
it is possible to search on the attribute (on the website), it can be written (which
means you can set it while placing an advertisement) and it can be updated (using a
PUT or PATCH request on the advertisement).

Step 2: Create the advertisement JSON
-------------------------------------

Based on the data for the category we want to place an advertisement in, we can construct
the json required to post an advertisement. To continue with the example of a CD player,
the JSON for a minimal advertisement should look something like this:

.. code-block:: JSON

  {
    "categoryId":35,
    "title":"Test cd player",
    "description":"This is an awesome cd-player",
    "condition":"Nieuw",
    "brand":"Sony",
    "properties":"Draagbaar",
    "delivery":"Ophalen",
    "priceModel":{
      "modelType":"fixed",
      "askingPrice":3241
    },
    "location":{
      "postcode":"1097DN"
    }
  }

There are a couple of fields which are required, regardless of the category the
advertisement is placed in: ``categoryId``, ``title``, ``description``, ``priceModel``
and ``location``. All the other fields are optional: ``condition``, ``brand``,
``properties`` and ``delivery`` are attributes of the category with id 35. Note
that the ``properties`` attribute is of the type ``LIST``, and can also contain
a array of values instead of a single value.

 The JSON should be send to the Marktplaats API using a ``POST`` request:

 .. code-block:: HTTP

  POST /v1/advertisements
  Host: api.marktplaats.nl
  Authorization: Bearer myaccesstoken
  Content-Type: application/json
  {
    "categoryId":35,
    "title":"Test cd player",
    "description":"This is an awesome cd-player",
    "condition":"Nieuw",
    "brand":"Sony",
    "properties":"Draagbaar",
    "delivery":"Ophalen",
    "priceModel":{
      "modelType":"fixed",
      "askingPrice":3241
    },
    "location":{
      "postcode":"1097DN"
    }
  }

If we do this in PHP, and we have a form which is containing the field names matching
the names of the API, it would look something like this:

.. code-block:: PHP

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

Most code is needed to correct the type of numeric fields (like ``categoryId``).
Note that the snippet above lacks any form of error handling. In any production
usage of the API, errors should be handled. The API returns a common error document
which contains both machine readable as well as human readable error messages.
When the language is set correctly in the request, using the ``accept-language``
header, the messages are returned in the correct language. At the moment the only
supported languages are Dutch and English.

Step 3: Adding images
---------------------

TODO

Step 4: Buying features
-----------------------

TODO

Step 5: Updating the advertisement
----------------------------------

TODO
