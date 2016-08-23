<?php
/**
 * Instant Analytics plugin for Craft CMS
 *
 * Instant Analytics is a turnkey Google analytics solution for Craft CMS
 *
 * @author    nystudio107
 * @copyright Copyright (c) 2016 nystudio107
 * @link      http://nystudio107.com
 * @package   InstantAnalytics
 * @since     1.0.0
 */

namespace Craft;

class InstantAnalyticsPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        require_once __DIR__ . '/vendor/autoload.php';
        Craft::import('plugins.InstantAnalytics.src.IAnalytics');

/* -- This is the hook that triggers a PageView to be sent */

        craft()->templates->hook('iaSendPageView', function(&$context)
        {
            if (craft()->request->isSiteRequest() && !craft()->isConsole())
            {
                if (isset($context['instantAnalytics']))
                {

    /* -- Get the Analytics object from the Twig context */

                    $analytics = $context['instantAnalytics'];

    /* -- If SEOmatic is installed, set the page title from it */

                    if ((craft()->plugins->getPlugin('Seomatic')) && (isset($context['seomaticMeta'])))
                    {
                        $seomaticMeta = $context['seomaticMeta'];
                        $analytics->setDocumentTitle($seomaticMeta['seoTitle']);
                    }

    /* -- Send the page view */

                    craft()->instantAnalytics->sendPageView($analytics);
                }
            }
            return "";
        });

/* -- Only install these listeners if Craft Commerce is installed */

        if (craft()->plugins->getPlugin('Commerce'))
        {
    /* -- Listen for completed Craft Commerce orders */

            craft()->on('commerce_orders.onOrderComplete', function(Event $e)
            {
                $orderModel = null;

                if (isset($e->params['order']))
                    $orderModel = $e->params['order'];
                craft()->instantAnalytics->orderComplete($orderModel);
            });

    /* -- Listen for items added to the Craft Commerce cart */

            craft()->on('commerce_cart.onAddToCart', function(Event $e)
            {
                $orderModel = null;
                $lineItem = null;

                if (isset($e->params['cart']))
                    $orderModel = $e->params['cart'];
                if (isset($e->params['lineItem']))
                    $lineItem = $e->params['lineItem'];
                craft()->instantAnalytics->addToCart($orderModel, $lineItem);
            });

    /* -- Listen for items removed from the Craft Commerce cart */

                if (craft()->commerce_cart->hasEvent('onBeforeRemoveFromCart'))
                {
                    craft()->on('commerce_cart.onBeforeRemoveFromCart', function(Event $e)
                    {
                        $orderModel = null;
                        $lineItem = null;

                        if (isset($e->params['cart']))
                            $orderModel = $e->params['cart'];
                        if (isset($e->params['lineItem']))
                            $lineItem = $e->params['lineItem'];
                        craft()->instantAnalytics->removeFromCart($orderModel, $lineItem);
                    });
                }
                else
                    InstantAnalyticsPlugin::log("commerce_cart.onBeforeRemoveFromCart doesn't exist", LogLevel::Info, false);

        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Instant Analytics');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Instant Analytics lets you track otherwise untrackable assets & events with Google Analytics, and eliminates the need for Javascript tracking');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'https://github.com/nystudio107/instantanalytics/blob/master/README.md';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/nystudio107/instantanalytics/master/releases.json';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.6';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'nystudio107';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://nystudio107.com';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.instantanalytics.twigextensions.InstantAnalyticsTwigExtension');

        return new InstantAnalyticsTwigExtension();
    }

    /**
     */
    public function onBeforeInstall()
    {
        $result = true;
        if (version_compare(PHP_VERSION, '5.5', '<'))
        {
            $result = false;
            $error = "Instant Analytics requires php 5.4.0 or later to operate";
            if (version_compare(craft()->getVersion(), '2.5', '<'))
                throw new Exception($error);
            else
                craft()->userSession->setError($error);
        }
        return $result;
    }

    /**
     */
    public function onAfterInstall()
    {

/* -- Show our "Welcome to Instant Analytics" message */

        craft()->request->redirect(UrlHelper::getCpUrl('instantanalytics/welcome'));
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    } /* -- onAfterUninstall */

    /**
     * @return array
     */
    protected function defineSettings()
    {
        $defaultTrackingId = "";
        if (craft()->plugins->getPlugin('Seomatic'))
        {
            $seomaticSettings = craft()->seomatic->getSettings(craft()->language);
            $defaultTrackingId = $seomaticSettings['googleAnalyticsUID'];
        }

        return array(
            'googleAnalyticsTracking' => array(AttributeType::String, 'label' => 'Google Analytics Tracking ID:', 'default' => $defaultTrackingId),
            'autoSendPageView' => array(AttributeType::String, 'label' => 'Auto Send Page View:', 'default' => true),
        );
    }

    /**
     */
    public function getSettingsUrl()
    {
        return "";
    } /* -- getSettingsUrl */

    /**
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('instantanalytics/InstantAnalytics_Settings', array(
           'settings' => $this->getSettings()
       ));
    }

    /**
     * @param mixed $settings  The Widget's settings
     *
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...

        return $settings;
    }

}