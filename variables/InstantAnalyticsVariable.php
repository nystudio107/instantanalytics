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
    public function getPageViewObject($url="", $title="")
    {
        return craft()->instantAnalytics->getPageViewObject($url, $title);
    } /* -- getPageViewObject */

    /**
     * Get an Event analytics object
     * @return Analytics object
     */
    function getEventObject($eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        return craft()->instantAnalytics->getEventObject($eventCategory, $eventAction, $eventLabel, $eventValue);
    } /* -- getEventObject */

    /**
     * Return an analytics object
     * @return Analytics object
     */
    public function getAnalyticsObject()
    {
        return craft()->instantAnalytics->getAnalyticsObject();
    } /* -- getAnalyticsObject */

    /**
     * Get a PageView tracking URL
     * @param  string $url the URL to track
     * @return string the tracking URL
     */
    function getPageViewTrackingUrl($url="", $title="")
    {
        return craft()->instantAnalytics->getPageViewTrackingUrl($url, $title);
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