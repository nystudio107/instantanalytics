<?php
/**
 * Instant Analytics Configuration
 *
 * Completely optional configuration settings for Instant Analytics if you want to customize some
 * of its more esoteric behavior, or just want specific control over things.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'instantanalytics.php' and make
 * your changes there.
 */

return array(

/**
 * Controls whether Instant Analytics will send analytics data.
 */
    "sendAnalyticsData" => true,

/**
 * Controls whether Instant Analytics will send analytics data when `devMode` is on.
 */
    "sendAnalyticsInDevMode" => true,

/**
 * Controls whether we should filter out bot UserGents.
 */
    "filterBotUserAgents" => true,

/**
 * Controls whether we should exclude users logged into an admin account from Analytics tracking.
 */
    "adminExclude" => false,

/**
 * Controls whether analytics that blocked from being sent should be logged to
 * craft/storage/runtime/logs/instantanalytics.log
 * These are always logged if `devMode` is on
 */
    "logExcludedAnalytics" => false,

/**
 * Contains an array of Craft user group handles to exclude from Analytics tracking.  If there's a match
 * for any of them, analytics data is not sent.
 */
    "groupExcludes" => array(
            "some_user_group_handle",
        ),

/**
 * Contains an array of keys that correspond to $_SERVER[] super-global array keys to test against.
 * Each item in the sub-array is tested against the $_SERVER[] super-global key via RegEx; if there's
 * a match for any of them, analytics data is not sent.  This allows you to filter based on whatever
 * information you want.
 * Reference: http://php.net/manual/en/reserved.variables.server.php
 * RegEx tester: http://regexr.com
 */
    "serverExcludes" => array(
        'REMOTE_ADDR' => array(
            "/^localhost$|^127(?:\.[0-9]+){0,2}\.[0-9]+$|^(?:0*\:)*?:?0*1$/",
            ),
        ),

);