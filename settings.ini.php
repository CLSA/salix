<?php
/**
 * settings.ini.php
 *
 * Defines initialization settings for salix.
 * DO NOT edit this file, to override these settings use settings.local.ini.php instead.
 * Any changes in the local ini file will override the settings found here.
 */

global $SETTINGS;

// tagged version
$SETTINGS['general']['application_name'] = 'salix';
$SETTINGS['general']['instance_name'] = $SETTINGS['general']['application_name'];
$SETTINGS['general']['version'] = '2.2';
$SETTINGS['general']['build'] = 'dee3872';

// the location of salix internal path
$SETTINGS['path']['APPLICATION'] = str_replace( '/settings.ini.php', '', __FILE__ );

// Salix does not use VOIP
$SETTINGS['voip']['enabled'] = false;
