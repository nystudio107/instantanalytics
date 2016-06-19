<?php
/**
 * Instant Analytics plugin for Craft CMS
 *
 * InstantAnalytics Service
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2016 nystudio107
 * @link      http://nystudio107.com
 * @package   InstantAnalytics
 * @since     1.0.0
 */

namespace Craft;

use \TheIconic\Tracking\GoogleAnalytics\Analytics;
use \Jaybizzle\CrawlerDetect\CrawlerDetect;

class InstantAnalyticsService extends BaseApplicationComponent
{

    /**
     * analytics() return an analytics object
     * @return Analytics object
     */
    public function analytics()
    {
        $analytics = $this->_getAnalyticsObj();
        return $analytics;
    } /* -- analytics */

    /**
     * Send a PageView
     */
    function sendPageView($url="")
    {
        if ($this->_shouldSendAnalytics())
        {
            if ($url == "")
                $url = craft()->request->url;
            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
                $analytics->setDocumentPath($url)
                    ->sendPageView();
            }
        }
    } /* -- sendPageView */

    /**
     * Send an Event
     */
    function sendEvent($eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        if ($this->_shouldSendAnalytics())
        {
            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
                $analytics->setEventCategory($eventCategory)
                    ->setEventAction($eventAction)
                    ->setEventLabel($eventLabel)
                    ->setEventValue($eventValue)
                    ->sendEvent();
            }
        }
    } /* -- sendEvent */

    /**
     * Get a PageView tracking URL
     * @param  string $url the URL to track
     * @return string the tracking URL
     */
    function getPageViewTrackingUrl($url)
    {
        $urlParams = array(
            'url' => urlencode($url),
            );
        $trackingUrl = UrlHelper::getActionUrl('instantAnalytics/trackPageViewUrl', $urlParams);
        return $trackingUrl;
    } /* -- getPageViewTrackingUrl */

    /**
     * Get an Event tracking URL
     * @param  string $url the URL to track
     * @return string the tracking URL
     */
    function getEventTrackingUrl($url, $eventCategory="", $eventAction="", $eventLabel="", $eventValue=0)
    {
        $urlParams = array(
            'url' => urlencode($url),
            'eventCategory' => urlencode($eventCategory),
            'eventAction' => urlencode($eventAction),
            'eventLabel' => urlencode($eventLabel),
            'eventValue' => urlencode($eventValue),
            );
        $trackingUrl = UrlHelper::getActionUrl('instantAnalytics/trackEventUrl', $urlParams);
        return $trackingUrl;
    } /* -- getEventTrackingUrl */

    /**
     * Send analytics information for the completed order
     */
    public function orderComplete($orderModel = null)
    {
        if ($orderModel)
        {
            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
            }
        }
    } /* -- orderComplete */

    /**
     * Send analytics information for the item added to the cart
     */
    public function addToCart($orderModel = null, $lineItem = null)
    {
        if ($lineItem)
        {
            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
            }
        }
    } /* -- addToCart */

    /**
     * Send analytics information for the item removed from the cart
     */
    public function removeFromCart($orderModel = null, $lineItemId = 0)
    {
        $analytics = $this->_getAnalyticsObj();
        if ($analytics)
        {
        }
    } /* -- removeFromCart */

    private function _shouldSendAnalytics()
    {
        $result = true;

        if (!craft()->config->get("sendAnalyticsData", "instantanalytics"))
            return false;
        if (!craft()->config->get("sendAnalyticsInDevMode", "instantanalytics") && craft()->config->get('devMode'))
            return false;
        if (craft()->isConsole())
            return false;
        if (craft()->request->isCpRequest())
            return false;
        if (craft()->request->isLivePreview())
            return false;

/* -- Check the $_SERVER[] super-global exclusions */

        $exclusions = craft()->config->get("serverExcludes", "instantanalytics");
        if (isset($exclusions) && is_array($exclusions))
        {
            foreach ($exclusions as $match => $matchArray)
            {
                if (isset($_SERVER[$match]))
                {
                    foreach ($matchArray as $matchItem)
                    {
                        if (preg_match($matchItem, $_SERVER[$match]))
                            return false;
                    }
                }
            }
        }

/* -- Filter out bot/spam requests via UserAgent */

        if (craft()->config->get("filterBotUserAgents", "instantanalytics"))
        {
            $CrawlerDetect = new CrawlerDetect;
// Check the user agent of the current 'visitor'
            if ($CrawlerDetect->isCrawler())
                return false;
        }

/* -- Filter by user group */

        $session = craft()->userSession;
        if ($session)
        {
            $user = $session->getUser();
            $exclusions = craft()->config->get("groupExcludes", "instantanalytics");

            if ($user && isset($exclusions) && is_array($exclusions))
            {
                if ($session->isLoggedIn())
                {
                    foreach ($exclusions as $matchItem)
                    {
                        if ($user->isInGroup($matchItem))
                            return false;
                        if ($matchItem == "admin" && $session->isAdmin())
                            return false;
                    }
                }
            }
        }

        return $result;
    }
    /**
     * Get the Google Analytics object, primed with the default values
     */
    private function _getAnalyticsObj()
    {
        $analytics = null;
        $settings = craft()->plugins->getPlugin('instantanalytics')->getSettings();
        if (isset($settings) && isset($settings['googleAnalyticsTracking']) && $settings['googleAnalyticsTracking'] != "")
        {
            $analytics = new Analytics();
            if ($analytics)
            {
                $analytics->setProtocolVersion('1')
                    ->setTrackingId($settings['googleAnalyticsTracking'])
                    ->setIpOverride($_SERVER['REMOTE_ADDR'])
                    ->setUserAgentOverride($_SERVER['HTTP_USER_AGENT'])
                    ->setAsyncRequest(true)
                    ->setClientId($this->gaParseCookie());

                $gclid = $this->getGclid();
                if ($gclid)
                    $analytics->GoogleAdwordsId($gclid);

            }
        }

        return $analytics;
    } /* -- _getAnalyticsObj */

    /**
     * setGclid set the 'gclid' cookie
     */
    function getGclid()
    {
        $gclid = "";
        if (isset($_GET['gclid']))
        {
            $gclid = $_GET['gclid'];
            if (!empty($gclid))
            {
                setcookie("gclid", $gclid, time() + (10 * 365 * 24 * 60 * 60));
            }
        }
        return $gclid;
    } /* -- setGclid */

    /**
     * gaParseCookie Handle the parsing of the _ga cookie or setting it to a unique identifier
     * @return string the cid
     */
    function gaParseCookie()
    {
        if (isset($_COOKIE['_ga']))
        {
            list($version, $domainDepth, $cid1, $cid2) = preg_split('[\.]', $_COOKIE["_ga"], 4);
            $contents = array('version' => $version, 'domainDepth' => $domainDepth, 'cid' => $cid1 . '.' . $cid2);
            $cid = $contents['cid'];
        }
        else
        {
            $cid = $this->gaGenUUID();
        }
        return $cid;
    } /* -- gaParseCookie */

    /**
     * gaGenUUID Generate UUID v4 function - needed to generate a CID when one isn't available
     * @return string The generated UUID
     */
    function gaGenUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    } /* -- gaGenUUID */

}