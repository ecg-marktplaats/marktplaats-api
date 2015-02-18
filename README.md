Welkom to the Marktplaats API
=============================

This repository contains documentation for the Marktplaats API and is used
to track public issues and feature requests, as well to publish examples on how to
use the API.

The documentation can be found here: http://ecg-marktplaats.github.io/marktplaats-api/
API Reference can be found here: https://api.marktplaats.nl/docs/v1/index.html

Issues can be filed here: https://github.com/ecg-marktplaats/marktplaats-api/issues

Announcements regarding our API can be found in our [Google+ community](https://plus.google.com/communities/107755456222203660866)

Changelog
=========

### Februari 2015

- Fixed constructionYear attribute (Bouwjaar) in all categories to support dates up to 2015
- Validation errors for incorrect stickerText on ad are displayed correctly
- When updating an advertisement using PUT or PATCH, it sometimes occured that the cityName was suddenly invalid. This is fixed.
- For API partners, it is now possible to use a longer sellerName in an advertisement and include 'url-like' names in a sellerName.
- User reviews from external partners (Klantenvertellen.nl and Tevreden.nl) are now exposed throught the API in /v1/users/<userId>/reviews. The settings endpoint for reviews is changed from /v1/users/<userId>/user-reviews to /v1/users/<userId>/reviews-settings. The feature 'klantreviews' has to be bought in order to see the reviews.
- __Authentication__ The confirm-access screen is now skipped when the user already approved the client in the past to access its data. With this change, when the user is already logged in (for example on the normal www.marktplaats.nl website) and approved the client in the past, there is no user interaction necessary to obtain an access_token.
