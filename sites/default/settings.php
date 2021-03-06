<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to insure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

$settings['hash_salt'] = 'CHANGE_THIS';

/**
 * Environment Indicator Settings
 * /admin/config/development/environment-indicator
 */
$config['environment_indicator.indicator']['bg_color'] = '#d10c0c';
$config['environment_indicator.indicator']['fg_color'] = '#fcf2f2';
$config['environment_indicator.indicator']['name'] = 'Dev Environment';
// <DDSETTINGS>
// Please don't edit anything between <DDSETTINGS> tags.
// This section is autogenerated by Acquia Dev Desktop.
if (isset($_SERVER['DEVDESKTOP_DRUPAL_SETTINGS_DIR']) && file_exists($_SERVER['DEVDESKTOP_DRUPAL_SETTINGS_DIR'] . '/loc_richard_jack_dd.inc')) {
  require $_SERVER['DEVDESKTOP_DRUPAL_SETTINGS_DIR'] . '/loc_richard_jack_dd.inc';
}
// </DDSETTINGS>
$settings['install_profile'] = 'standard';
