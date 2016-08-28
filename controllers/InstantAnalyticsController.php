<?php
/**
 * Instant Analytics plugin for Craft CMS
 *
 * InstantAnalytics Controller
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2016 nystudio107
 * @link      https://nystudio107.com
 * @package   InstantAnalytics
 * @since     1.0.0
 */

namespace Craft;

class InstantAnalyticsController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array('actionTrackPageViewUrl',
        'actionTrackEventUrl',
        );

    /**
     */
    public function actionTrackPageViewUrl()
    {
        $url = urldecode(craft()->request->getParam('url'));
        $title = urldecode(craft()->request->getParam('title'));
        $analytics = craft()->instantAnalytics->pageViewAnalytics($url, $title);
        $analytics->sendPageView();
        craft()->request->redirect($url, true, 200);
    } /* -- actionTrackPageViewUrl */

    /**
     */
    public function actionTrackEventUrl()
    {
        $url = urldecode(craft()->request->getParam('url'));
        $eventCategory = urldecode(craft()->request->getParam('eventCategory'));
        $eventAction = urldecode(craft()->request->getParam('eventAction'));
        $eventLabel = urldecode(craft()->request->getParam('eventLabel'));
        $eventValue = urldecode(craft()->request->getParam('eventValue'));
        $analytics = craft()->instantAnalytics->eventAnalytics($eventCategory, $eventAction, $eventLabel, $eventValue);
        $analytics->sendEvent();
        craft()->request->redirect($url, true, 200);
    } /* -- actionTrackPageViewUrl */

}