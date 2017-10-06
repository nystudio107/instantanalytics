# Instant Analytics Changelog

## 1.1.11 - 2017.10.06
### Changed
* [Fixed] Fixed a regression with product variants

## 1.1.10 - 2017.09.21
### Changed
* [Fixed] Fixed an issue with Digital Products (and potentially other types of third-party purchasables)

## 1.1.9 - 2017.08.24
### Changed
* [Fixed] Fixed the default DocumentReferrer that could have caused spurious analytic results in some cases
* [Improved] Updated to the latest composer dependencies

## 1.1.8 - 2017.06.24
### Changed
* [Fixed] Fix for error when passing variant to `addCommerceProductDetailView`
* [Improved] Installed the latest Composer dependencies

### Added
* [Added] Added support for category fields in products

## 1.1.7 - 2017.03.21

* [Improved] Wrap calls to `sendHit()` in a `try` / `catch` block to avoid hard errors

## 1.1.6 - 2017.01.21

* [Fixed] Fixed an issue where the `LIST_INDEX` wasn't being passed down to `addCommerceProductImpression`
* [Fixed] Fixed an issue that would cause only one item to be listed on Product Lists
* [Improved] Broke out the changelog into CHANGELOG.md

## 1.1.5 - 2016.12.21

* [Improved] Fixed the Analytics Excludes
* [Added] Added support for DigitalProducts and other base purchaseables
* [Added] Added `UptimeRobot` to the crawlers list
* [Improved] Rolled the multi-env aware `config.php` back; Craft doesn't work that way
* [Improved] Updated README.md

## 1.1.4 - 2016.12.20

* [Fixed] `addCommerceProductImpression()` now works properly
* [Improved] Made the default `config.php` multi-environment
* [Added] Added extensive logging of excluded analytics data
* [Added] Added `logExcludedAnalytics` config.php setting
* [Fixed] Fixed an issue that would cause InstantAnalytics to not filter out bots properly
* [Improved] `DocumentHostName` is now set by default
* [Improved] Fixed a CSS file typo in the `welcome.twig` template
* [Improved] Updated to the latest vendor deps
* [Improved] Updated README.md

## 1.1.3 - 2016.09.23

* [Improved] Don't redirect to the welcome page if we're being installed via Console command
* [Improved] Updated README.md

## 1.1.2 - 2016.09.18

* [Improved] The URLs returned by `pageViewTrackingUrl()` and `eventTrackingUrl()` will now work even if there is no filename in the URL
* [Improved] Added a global config option to strip the query string from PageView URLs
* [Improved] The `_ia` cookie is now set on `/`
* [Fixed] Tracking URL params are no longer double url encoded
* [Improved] Updated README.md

## 1.1.1 - 2016.08.29

* [Added] Added `Category` and `Brand` fields to the Settings, so you can specify what fields this data for Google Enhanced Ecommerce should be pulled from
* [Improved] Changed the PageView Tracking URL and Event Tracking URL format so that they can be included in RSS feeds directly
* [Improved] We do a better job checking to see if the Commerce and SEOmatic plugins are installed
* [Added] If SEOmatic is installed, we automatically do a `setAffiliation` for the Analytics object, using the `siteSeoName`
* [Added] You can now discretely choose which Google Enhanced Ecommerce events are automatically sent
* [Added] A product's `Category` is by default set to the name of the Product Type
* [Improved] Updated README.md

## 1.1.0 - 2016.08.26

* [Added] Refactored the code so that an `instantAnalytics` object is now injected into your templates so you can manipulate it as you see fit, and then use `{% hook 'iaSendPageView' %}` to send the PageView
* [Added] Automatic Google Enhanced Ecommerce tracking of Craft Commerce OrderComplete
* [Added] Automatic Google Enhanced Ecommerce tracking of Craft Commerce AddToCart
* [Added] Automatic Google Enhanced Ecommerce tracking of Craft Commerce RemoveFromCart
* [Added] Added addCommerceProductImpression() to the IAnalytics object to allow for sending of product impressions
* [Added] Added addCommerceProductDetailView() to the IAnalytics object to allow for sending of product detail views
* [Added] Added addCommerceCheckoutStep() to the IAnalytics object to allow for sending of cart checkout steps
* [Improved] Updated to the latest Google Measurement Protocol (2.3.0)
* [Improved] Added debug logging when in `devMode` for any Analytics data that is sent
* [Improved] Fall back on a default User Agent when `$_SERVER['HTTP_USER_AGENT']` is not set
* [Added] We now send HTTP_REFERER by default
* [Improved] Updated README.md

## 1.0.5 - 2016.08.16

* [Fixed] If there is no _ga cookie set, we generate a UUID and set it, to allow sessions to work correctly
* [Added] Added the $title parameter to sendPageView()
* [Improved] Updated README.md

## 1.0.4 - 2016.08.08

* [Added] Added an adminExclude setting in config.php for anyone logged in using an admin for Analytics data exclusion
* [Improved] AI now ensures that the pageview URLs are not absolute URLs
* [Improved] Updated README.md

## 1.0.3 - 2016.06.19

* [Added] Added a bot UserAgent filter list (on by default), configurable via filterBotUserAgents in config.php
* [Added] Added groupExcludes setting in config.php that has an array of Craft user group handles for Analytics data exclusion
* [Added] Added serverExcludes setting in config.php that has arrays of $_SERVER[] superglobal RegEx tests for Analytics data exclusion
* [Improved] The various _shouldSendAnalytics() tests now short-circuit, returning as soon as a false condition is met
* [Improved] Updated README.md

## 1.0.2 - 2016.06.16

* [Fixed] The UserAgent is now set to the client UserAgent by default
* [Improved] Added a sendAnalyticsData setting to config.php
* [Improved] Updated README.md

## 1.0.1 - 2016.06.14

* [Added] Updated to a newer version of the GAMP lib for PHP, which uses Guzzle 6.x
* [Fixed] No more Composer dependency conflicts with the Oauth 2.0 plugin
* [Improved] Updated README.md

## 1.0.0 - 2016.06.13

* Initial release

Brought to you by [nystudio107](http://nystudio107.com)