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
    /**
     * Turn an empty value so the twig tags {{ }} can be used
     * @return string ""
     */
    public function __toString()
    {
        return "";
    } /* -- __toString */

    /**
     * Add a product impression to the Analytics object
     * @param Commerce_ProductModel or Commerce_VariantModel  $productVariant the Product or Variant
     */
    public function addCommerceProductImpression($productVariant = null)
    {
        if ($productVariant)
        {
            craft()->instantAnalytics->addCommerceProductImpression($this, $productVariant);
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