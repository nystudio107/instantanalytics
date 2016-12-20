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

class IAnalytics extends Analytics
{

    protected $shouldSendAnalytics = null;

    /**
     * Override __construct() to store whether or not we should be sending Analytics data
     *
     * @param bool $isSsl
     */
    public function __construct($isSsl = false)
    {
        $this->shouldSendAnalytics = craft()->instantAnalytics->shouldSendAnalytics();
        return parent::__construct($isSsl);
    } /* -- __construct */

    /**
     * Turn an empty value so the twig tags {{ }} can be used
     * @return string ""
     */
    public function __toString()
    {
        return "";
    } /* -- __toString */

    /**
     * Override sendHit() so that we can prevent Analytics data from being sent
     *
     * @param $methodName
     * @return AnalyticsResponse
     */
    protected function sendHit($methodName)
    {
        $loggingFlag = craft()->config->get("logExcludedAnalytics", "instantanalytics");
        $requestIp = $_SERVER['REMOTE_ADDR'];
        if ($this->shouldSendAnalytics)
            return parent::sendHit($methodName);
        else
        {
            InstantAnalyticsPlugin::log("*** sendHit(): analytics not sent for " . $requestIp, LogLevel::Info, $loggingFlag);
            return null;
        }
    } /* -- sendHit */

    /**
     * Add a product impression to the Analytics object
     * @param Commerce_ProductModel or Commerce_VariantModel  $productVariant the Product or Variant
     * @param int  $index Where the product appears in the list
     */
    public function addCommerceProductImpression($productVariant = null, $index = 0, $listName = "default", $listIndex = 1)
    {

        if ($productVariant)
        {
            craft()->instantAnalytics->addCommerceProductImpression($this, $productVariant, $index, $listName);
        }

    } /* -- addCommerceProductImpression */

    /**
     * Add a product detail view to the Analytics object
     * @param Commerce_ProductModel or Commerce_VariantModel  $productVariant the Product or Variant
     */
    public function addCommerceProductDetailView($productVariant = null)
    {
        if ($productVariant)
        {
            craft()->instantAnalytics->addCommerceProductDetailView($this, $productVariant);
        }
    } /* -- addCommerceProductDetailView */

    /**
     * Add a checkout step to the Analytics object
     * @param Commerce_ProductModel or Commerce_VariantModel  $productVariant the Product or Variant
     */
    public function addCommerceCheckoutStep($orderModel = null, $step = 1, $option = "")
    {
        if ($orderModel)
        {
            craft()->instantAnalytics->addCommerceCheckoutStep($this, $orderModel, $step, $option);
        }
    } /* -- addCommerceCheckoutStep */

}