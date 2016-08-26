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
     * Get a PageView analytics object
     * @return Analytics object
     */
    function pageViewAnalytics($url="", $title="")
    {
        return craft()->instantAnalytics->pageViewAnalytics($url, $title);
    } /* -- pageViewAnalytics */

    /**
     * Get an Event analytics object
     * @return Analytics object
     */
    function eventAnalytics($eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        return craft()->instantAnalytics->eventAnalytics($eventCategory, $eventAction, $eventLabel, $eventValue);
    } /* -- eventAnalytics */

    /**
     * Return an Analytics object
     */
    public function analytics()
    {
        return craft()->instantAnalytics->analytics();
    }

    /**
     * Get a PageView tracking URL
     * @param  string $url the URL to track
     * @param  string $title the page title
     * @return string the tracking URL
     */
    function pageViewTrackingUrl($url, $title)
    {
        return craft()->instantAnalytics->pageViewTrackingUrl($url, $title);
    } /* -- pageViewTrackingUrl */

    /**
     * Get an Event tracking URL
     * @param  string $url the URL to track
     * @param  string $eventCategory the event category
     * @param  string $eventAction the event action
     * @param  string $eventLabel the event label
     * @param  string $eventValue the event value
     * @return string the tracking URL
     */
    function eventTrackingUrl($url, $eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        return craft()->instantAnalytics->eventTrackingUrl($url, $eventCategory, $eventAction, $eventLabel, $eventValue);
    } /* -- eventTrackingUrl */

}