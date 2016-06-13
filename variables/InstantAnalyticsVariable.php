<?php
/**
 * Instant Analytics plugin for Craft CMS
 *
 * Instant Analytics Variable
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2016 nystudio107
 * @link      http://nystudio107.com
 * @package   InstantAnalytics
 * @since     1.0.0
 */

namespace Craft;

class InstantAnalyticsVariable
{
    /**
     * Return an Analytics object
     */
    public function analytics()
    {
        return craft()->instantAnalytics->analytics();
    }

    /**
     * Send a PageView
     */
    public function sendPageView($url="")
    {
        return craft()->instantAnalytics->sendPageView($url);
    }

    /**
     * Send an Event
     */
    function sendEvent($eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        return craft()->instantAnalytics->sendEvent($eventCategory, $eventAction, $eventLabel, $eventValue);
    } /* -- sendEvent */

    /**
     * Get a PageView tracking URL
     * @param  string $url the URL to track
     * @return string the tracking URL
     */
    function getPageViewTrackingUrl($url)
    {
        return craft()->instantAnalytics->getPageViewTrackingUrl($url);
    } /* -- getPageViewTrackingUrl */

    /**
     * Get an Event tracking URL
     * @param  string $url the URL to track
     * @return string the tracking URL
     */
    function getEventTrackingUrl($url, $eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        return craft()->instantAnalytics->getEventTrackingUrl($url, $eventCategory, $eventAction, $eventLabel, $eventValue);
    } /* -- getEventTrackingUrl */

}