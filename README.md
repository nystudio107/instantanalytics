# Instant Analytics plugin for Craft CMS

Instant Analytics lets you track otherwise untrackable assets & events with Google Analytics, and eliminates the need for Javascript tracking

![Screenshot](resources/screenshots/ia_screenshot01.png)

## Installation

To install Instant Analytics, follow these steps:

1. Download & unzip the file and place the `instantanalytics` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/nystudio107/instantanalytics.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3. -OR- install with Composer via `composer require nystudio107/instantanalytics`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `instantanalytics` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Instant Analytics works on Craft 2.4.x, Craft 2.5.x, and Craft 2.6.x.  It requires at least PHP 5.5 or later to work.

## Instant Analytics Overview

Instant Analytics lets you track otherwise untrackable assets & events with Google Analytics, and eliminates the need for Javascript tracking.

You don't need to include the typical Google Analytics script tag, instead Instant Analytics will automatically send page views when your front-end templates are rendered, or you can do it manually via a simple Twig tag.

You can also track asset/media views in Google Analytics, either as PageViews or as Events. This lets you track otherwise untrackable things such as individual RSS feed accesses, images, PDF files, etc.

## Use Cases

### Simple Page Tracking

If all you want is simple page tracking data sent to Google Analytics, Instant Analytics will do that for you automatically.  Instant Analytics uses the [Google Measurement Protocol](https://developers.google.com/analytics/devguides/collection/protocol/v1/) to send PageViews to your Google Analytics account the same way the Google Analytics Tracking Code Javascript tag does.

It has the added benefit of not having to load any Javascript on the front-end to do this, which results in the following benefits:

* Your pages will render quicker in-browser, with no external resources loaded just for PageView tracking
* Pages will be tracked even if the client's browser has Javascript disabled or blocked
* Javascript errors will not cause Google Analytics data to fail to be collected

### Tracking Assets/Resources

Instant Analytics lets you track assets/resources that you can't normally track.  For instance, you may have a collection of PDF documents that you'd like to know when they are viewed.

Using a simple `{{ getPageViewTrackingUrl(myAsset.url) }}` or `{{ getEventTrackingUrl(myAsset.url, "category", "action", "label", "value") }}` Twig function, Instant Analytics will generate a public URL that will register a PageView in Google Analytics for the asset/resource, and then display or download the asset/resource.

### Tracking RSS Feeds

Getting actual tracking statistics on RSS feeds can be notoriously difficult, because they are often consumed by clients that are not web browsers, and therefor will not run Javascript tracking code.

With Instant Analytics, if your RSS feed is a Twig template, accesses will automatically be tracked.  Additionally, you can use the `{{ getPageViewTrackingUrl(myAsset.url) }}` or `{{ getEventTrackingUrl(myAsset.url, "category", "action", "label", "value") }}` Twig functions to track individual episode accesses in Google Analytics.

### Custom Tracking via Twig or Plugin

If your needs are more specialized, Instant Analytics will give your Twig templates or plugin full access to an `Analytics` object that allows you to send arbitrary Google Analytics tracking data to Google Analytics.

You can do anything from customized PageViews to complicated Google Enhanced eCommerce tracking, 

## Configuring Instant Analytics

Once you have installed Instant Analytics, you'll see a welcome screen.  Click on **Get Started** to configure Instant Analytics:

* **Google Analytics Tracking ID:** Enter your Google Analytics Tracking ID here. Only enter the ID, e.g.: UA-XXXXXX-XX, not the entire script code.
* **Auto Send Page View:** If this setting is on, PageViews are automatically sent to Google Analytics whenever your front-end templates are rendered.

If you have the [SEOmatic](https://github.com/nystudio107/seomatic) plugin installed, Instant Analytics will automatically grab your **Google Analytics Tracking ID:** from it.

**NOTE:** Instant Analytics will work with the traditional Google Analytics Tracking Code Javascript tag; it's not an either/or, they can coexist.  Instant Analytics is just a different way to send the same data to Google Analytics.  However, if you use the **Auto Send Page View:** feature in Instant Analytics, you should turn off the Javascript sending PageViews automatically by:

* In [SEOmatic](https://github.com/nystudio107/seomatic) turn off **Automatically send Google Analytics PageView**
* If you don't use SEOmatic, remove the line `ga('send', 'pageview');` from your Google Analytics Tracking Code Javascript tag

## Using Instant Analytics

### Simple Page Tracking

To do simple PageView tracking you literally have to do nothing.  Once you've entered your **Google Analytics Tracking ID:** it will automatically send PageViews when your front-end templates are loaded, if you have **Auto Send Page View:** turned on.

It does not send any Google Analytics data if:

* You have not entered a valid **Google Analytics Tracking ID:**
* You are viewing templates in Live Preview
* `devMode` is on (you can change this behavior in the `config.php` file)
* The request is a CP or Console request
* If you have `sendAnalyticsData` set to false in the `config.php` file

If you want to manually send a page view, you can do that via either:

    {{ sendPageView(URL) }}
    -OR-
    {{ craft.instantAnalytics.sendPageView(URL) }}
    
By default if you provide no `URL` parameter, the current request is used, so it's as simple as just: `{{ sendPageView }}`

### Sending Events

You can send Events to Google Analytics via either:

    {{ sendEvent(CATEGORY, ACTION, LABEL, VALUE) }}
    -OR-
    {{ craft.instantAnalytics.sendEvent(CATEGORY, ACTION, LABEL, VALUE) }}

What `CATEGORY`, `ACTION`, `LABEL`, and `VALUE` are is completely up to you; you can provide whatever data makes sense for your application, and view it in Google Analytics.  See [Event Tracking](https://developers.google.com/analytics/devguides/collection/analyticsjs/events) for more information.

### Tracking Assets/Resources

Instant Analytics lets you track assets/resources that you can't normally track, by providing a tracking URL that you use in your front-end templates.

You can track as PageViews via either:

    {{ getPageViewTrackingUrl(URL) }}
    -OR-
    {{ craft.instantAnalytics.getPageViewTrackingUrl(URL) }}

Or you can track as Events via either:

    {{ getEventTrackingUrl(URL, CATEGORY, ACTION, LABEL, VALUE) }}
    -OR-
    {{ craft.instantAnalytics.getEventTrackingUrl(URL, CATEGORY, ACTION, LABEL, VALUE) }}

These can be wrapped around any URL, so you could wrap your tracking URL around an image, a PDF, or an externally linked file... whatever.

What happens when the link is clicked on is Instant Analytics sends the tracking PageView or Event to Google Analytics, and then the original URL is seamlessly accessed.

### Custom Tracking via Twig or Plugin

If your needs are more specialized, you can build arbitrary Google Analytics data packets with Instant Analytics.  To get an `Analytics` object do the following:

Twig:

    {% set myAnalytics = craft.instantAnalytics.analytics() %}

PHP via Plugin:

    $myAnalytics = craft()->instantAnalytics->analytics();

In either case, you will be returned an `Analytics` object that is initialized with the following settings for you:

    $myAnalytics->setProtocolVersion('1')
        ->setTrackingId(YOUR_TRACKING_ID)
        ->setIpOverride($_SERVER['REMOTE_ADDR'])
        ->setAsyncRequest(true)
        ->setClientId(CLIENT_CID);
        ->GoogleAdwordsId(CLIENT_GCLID);

You are then free to change any of the parameters as you see fit via the [Google Analytics Measurement Protocol library for PHP](https://github.com/theiconic/php-ga-measurement-protocol)

Here's a simple example where we send a PageView for a specific page (after adding an Affiliation):

Twig:

    {% set myAnalytics = craft.instantAnalytics.analytics() %}
    {{ myAnalytics.setDocumentPath('/some/page').setAffiliation('nystudio107').sendPageview() }}

PHP via Plugin:

    $myAnalytics = craft()->instantAnalytics->analytics();
    $myAnalytics->setDocumentPath('/some/page')
        ->setAffiliation('nystudio107')
        ->sendPageview();

The sky's the limit in either case, you can do anything from simple PageViews to complicated Google Enhanced eCommerce analytics tracking.

## Instant Analytics Roadmap

Some things to do, and ideas for potential features:

* Add automatic Google Enhanced eCommerce Tracking integration with Craft Commerce
* Release it

## Instant Analytics Changelog

### 1.0.3 -- 2016.06.19

* [Added] Added a bot UserAgent filter list (on by default), configurable via filterBotUserAgents in config.php
* [Added] Added groupExcludes setting in config.php that has an array of Craft user group handles for Analytics data exclusion
* [Added] Added serverExcludes setting in config.php that has arrays of $_SERVER[] superglobal RegEx tests for Analytics data exclusion
* [Improved] The various _shouldSendAnalytics() tests now short-circuit, returning as soon as a false condition is met
* [Improved] Updated README.md

### 1.0.2 -- 2016.06.16

* [Fixed] The UserAgent is now set to the client UserAgent by default
* [Improved] Added a sendAnalyticsData setting to config.php
* [Improved] Updated README.md

### 1.0.1 -- 2016.06.14

* [Added] Updated to a newer version of the GAMP lib for PHP, which uses Guzzle 6.x
* [Fixed] No more Composer dependency conflicts with the Oauth 2.0 plugin
* [Improved] Updated README.md

### 1.0.0 -- 2016.06.13

* Initial release

Brought to you by [nystudio107](http://nystudio107.com)