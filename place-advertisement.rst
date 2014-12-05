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
is specified, an error will be produced.
