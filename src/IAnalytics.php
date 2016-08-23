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
    public function __toString()
    {
        return "";
    }
}