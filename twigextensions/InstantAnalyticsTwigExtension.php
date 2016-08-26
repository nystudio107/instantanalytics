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
        $result = array();
        if (craft()->request->isSiteRequest() && !craft()->isConsole())
        {

/* -- Return our Analytics object as a Twig global */

            $currentTemplate = $this->_get_current_template_path();
            $result = craft()->instantAnalytics->getGlobals($currentTemplate);
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'pageViewAnalytics' => new \Twig_Filter_Method($this, 'pageViewAnalytics'),
            'eventAnalytics' => new \Twig_Filter_Method($this, 'eventAnalytics'),
/* -- For namespacing reasons, perhaps we should not do this
            'analytics' => new \Twig_Filter_Method($this, 'analytics'),
*/
            'pageViewTrackingUrl' => new \Twig_Filter_Method($this, 'pageViewTrackingUrl'),
            'eventTrackingUrl' => new \Twig_Filter_Method($this, 'eventTrackingUrl'),
        );
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'pageViewAnalytics' => new \Twig_Function_Method($this, 'pageViewAnalytics'),
            'eventAnalytics' => new \Twig_Function_Method($this, 'eventAnalytics'),
            'analytics' => new \Twig_Function_Method($this, 'analytics'),
            'pageViewTrackingUrl' => new \Twig_Function_Method($this, 'pageViewTrackingUrl'),
            'eventTrackingUrl' => new \Twig_Function_Method($this, 'eventTrackingUrl'),
        );
    }

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

    /**
     * Get the current template path
     * @return string the template path
     */
    private function _get_current_template_path()
    {
        $result = "";
        $currentTemplate = craft()->templates->getRenderingTemplate();
        $templatesPath = method_exists(craft()->templates, 'getTemplatesPath') ? craft()->templates->getTemplatesPath() : craft()->path->getTemplatesPath();

        $path_parts = pathinfo($currentTemplate);

        if ($path_parts && isset($path_parts['dirname'])  && isset($path_parts['filename']))
        {
            $result = $path_parts['dirname'] . "/" . $path_parts['filename'];

            if (substr($result, 0, strlen($templatesPath)) == $templatesPath)
                {
                    $result = substr($result, strlen($templatesPath));
                }
        }
        return $result;
    } /* -- _get_current_template_path */

}