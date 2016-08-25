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
     * Add a product impression to the object
     * @param Commerce_ProductModel $product the product to add an impression for
     */
    public function addProductImpressionToPageView($product = null)
    {
        if ($product)
        {
            craft()->instantAnalytics->addProductImpression($this, $product);
        }
    } /* -- addProductImpression */
}