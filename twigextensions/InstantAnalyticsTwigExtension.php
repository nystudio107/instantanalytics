<?php
/**
 * Instant Analytics plugin for Craft CMS
 *
 * Instant Analytics Twig Extension
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2016 nystudio107
 * @link      https://nystudio107.com
 * @package   InstantAnalytics
 * @since     1.0.0
 */

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class InstantAnalyticsTwigExtension extends \Twig_Extension
{
    /**
     * @return string The extension name
     */
    public function getName()
    {
        return 'InstantAnalytics';
    }

    /**
     * @return array
     */
    public function getGlobals()
    {

/* -- Send an automatic pageView if it's set to in our config */

        $settings = craft()->plugins->getPlugin('instantanalytics')->getSettings();
        if (isset($settings) && isset($settings['autoSendPageView']) && $settings['autoSendPageView'])
            craft()->instantAnalytics->sendPageView();

        return array(
        );
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'analytics' => new \Twig_Filter_Method($this, 'analytics'),
            'sendPageView' => new \Twig_Filter_Method($this, 'sendPageView'),
            'sendEvent' => new \Twig_Filter_Method($this, 'sendEvent'),
            'getPageViewTrackingUrl' => new \Twig_Filter_Method($this, 'getPageViewTrackingUrl'),
            'getEventTrackingUrl' => new \Twig_Filter_Method($this, 'getEventTrackingUrl'),
        );
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'analytics' => new \Twig_Function_Method($this, 'analytics'),
            'sendPageView' => new \Twig_Function_Method($this, 'sendPageView'),
            'sendEvent' => new \Twig_Function_Method($this, 'sendEvent'),
            'getPageViewTrackingUrl' => new \Twig_Function_Method($this, 'getPageViewTrackingUrl'),
            'getEventTrackingUrl' => new \Twig_Function_Method($this, 'getEventTrackingUrl'),
        );
    }

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