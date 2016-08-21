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
use \TheIconic\Tracking\GoogleAnalytics\Parameters\EnhancedEcommerce\ProductAction as ProductAction;

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
    function sendPageView($url="", $title="")
    {
        if ($this->_shouldSendAnalytics())
        {
            if ($url == "")
                $url = craft()->request->url;

/* -- We want to send just a path to GA for page views */

            if (UrlHelper::isAbsoluteUrl($url))
            {
                $urlParts = parse_url($url);
                if (isset($urlParts['path']))
                    $url = $urlParts['path'];
                else
                    $url = "/";
                if (isset($urlParts['query']))
                    $url = $url . "?" . $urlParts['query'];
            }

/* -- We don't want to send protocol-relative URLs either */

            if (UrlHelper::isProtocolRelativeUrl($url))
            {
                $url = substr($url, 1);
            }

/* -- Prepare the Analytics object, and send the pageview */

            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
                $analytics->setDocumentPath($url)
                    ->setDocumentTitle($title)
                    ->sendPageView();
                InstantAnalyticsPlugin::log("sendPageView for `" . $url . "` - `" . $title . "`", LogLevel::Info, false);
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
                InstantAnalyticsPlugin::log("sendEvent for `" . $eventCategory . "` - `" . $eventAction . "` - `" . $eventLabel . "` - `" . $eventValue . "`", LogLevel::Info, false);
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
                // Then, include the transaction data
                $analytics->setTransactionId($orderModel->number)
                    ->setRevenue($orderModel->totalPrice)
                    ->setTax($orderModel->TotalTax)
                    ->setShipping($orderModel->totalShippingCost);

                // Coupon code?
                if ($orderModel->couponCode)
                    $analytics->setCouponCode($orderModel->couponCode);

                // Add each line item in the transaction
                // Two cases - variant and non variant products
                foreach ($orderModel->lineItems as $key => $lineItem)
                {

                    //This is the same for both variant and non variant products
                    $productData = [
                        'sku' => $lineItem->purchasable->sku,
                        'price' => $lineItem->salePrice,
                        'quantity' => $lineItem->qty,
                    ];

                    if (!$lineItem->purchasable->product->type->hasVariants)
                    {
                    //No variants (i.e. default variant)
                        $productData['name'] = $lineItem->purchasable->title;
                    }
                    else
                    {
                    // Product with variants
                        $productData['name'] = $lineItem->purchasable->product->title;
                        $productData['variant'] = $lineItem->purchasable->title;
                    }

                    //Add each product to the hit to be sent
                    $analytics->addProduct($productData);
                }

                // Don't forget to set the product action, in this case to PURCHASE
                $analytics->setProductActionToPurchase();

                // Finally, you must send a hit, in this case we send an Event
                $analytics->setEventCategory('Commerce')
                    ->setEventAction('Purchase')
                    ->setEventLabel($orderModel->number)
                    ->setEventValue($orderModel->totalPrice)
                    ->sendEvent();
                InstantAnalyticsPlugin::log("orderComplete for `Commerce` - `Purchase` - `" . $orderModel->number . "` - `" . $orderModel->totalPrice . "`", LogLevel::Info, false);
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
                //This is the same for both variant and non variant products
                $productData = [
                    'sku' => $lineItem->purchasable->sku,
                    'price' => $lineItem->salePrice,
                    'quantity' => $lineItem->qty,
                ];

                if (!$lineItem->purchasable->product->type->hasVariants)
                {
                //No variants (i.e. default variant)
                    $productData['name'] = $lineItem->purchasable->title;
                }
                else
                {
                // Product with variants
                    $productData['name'] = $lineItem->purchasable->product->title;
                    $productData['variant'] = $lineItem->purchasable->title;
                }

                //Add each product to the hit to be sent
                $analytics->addProduct($productData);

                // Don't forget to set the product action, in this case to ADD
                $analytics->setProductActionToAdd();

                // Finally, you must send a hit, in this case we send an Event
                $analytics->setEventCategory('Commerce')
                    ->setEventAction('Add to Cart')
                    ->setEventLabel($productData['name'])
                    ->setEventValue($productData['quantity'])
                    ->sendEvent();

                InstantAnalyticsPlugin::log("addToCart for `Commerce` - `Add to Cart` - `" . $productData['name'] . "` - `" . $productData['quantity'] . "`", LogLevel::Info, false);
            }
        }
    } /* -- addToCart */

    /**
     * Send analytics information for the item removed from the cart
     */
    public function removeFromCart($orderModel = null, $lineItemId = 0)
    {
        if ($lineItemId)
        {
            $analytics = $this->_getAnalyticsObj();
            if ($analytics)
            {
    /* Somehow, we need to get information on the lineItem that was removed from a lineItemId
                //This is the same for both variant and non variant products
                $productData = [
                    'sku' => $lineItem->purchasable->sku,
                    'price' => $lineItem->salePrice,
                    'quantity' => $lineItem->qty,
                ];

                if (!$lineItem->purchasable->product->type->hasVariants)
                {
                //No variants (i.e. default variant)
                    $productData['name'] = $lineItem->purchasable->title;
                }
                else
                {
                // Product with variants
                    $productData['name'] = $lineItem->purchasable->product->title;
                    $productData['variant'] = $lineItem->purchasable->title;
                }

                //Add each product to the hit to be sent
                $analytics->addProduct($productData);            }
    */
                // Don't forget to set the product action, in this case to REMOVE
                $analytics->setProductActionToRemove();

                // Finally, you must send a hit, in this case we send an Event
                $analytics->setEventCategory('Commerce')
                    ->setEventAction('Remove from Cart')
    /*
                    ->setEventLabel($productData['name'])
                    ->setEventValue($productData['quantity'])
    */
                    ->sendEvent();

                InstantAnalyticsPlugin::log("removeFromCart for `Commerce` - `Remove from Cart` - `" . $lineItemId . "`", LogLevel::Info, false);
            }
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

            if (craft()->config->get("adminExclude", "instantanalytics") && $session->isAdmin())
                return false;

            if ($user && isset($exclusions) && is_array($exclusions))
            {
                if ($session->isLoggedIn())
                {
                    foreach ($exclusions as $matchItem)
                    {
                        if ($user->isInGroup($matchItem))
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
            if (isset($_COOKIE['_ia']) && $_COOKIE['_ia'] !='' )
                $cid = $_COOKIE['_ia'];
            else
                $cid = $this->gaGenUUID();
        }
        setcookie('_ia', $cid, time()+60*60*24*730); // Two years
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