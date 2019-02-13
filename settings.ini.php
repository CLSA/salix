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
$SETTINGS['general']['version'] = '2.4';
$SETTINGS['general']['build'] = '12f5aea';

// the location of salix internal path
$SETTINGS['path']['APPLICATION'] = str_replace( '/settings.ini.php', '', __FILE__ );

// the location of the php_util repository
$SETTINGS['path']['PHP_UTIL'] = $SETTINGS['path']['APPLICATION'].'/../php_util';

// the location of opal views in json format
$SETTINGS['path']['OPAL_VIEWS'] = $SETTINGS['path']['APPLICATION'].'/aux/opal_views/json';

// the location of deployment reports (defaults to salix/doc/deployment_report)
$SETTINGS['path']['DEPLOYMENT_REPORT'] = str_replace( 'settings.ini.php', 'doc/deployment_report', __FILE__ );

// the location of the SSH key file used to communicate with APEX hosts (defaults to salix/doc/key)
$SETTINGS['apex']['apex_ssh_key'] = str_replace( 'settings.ini.php', 'doc/key', __FILE__ );

// the maximum rank to receive and process deployments
$SETTINGS['apex']['maximum_rank'] = 1;

// the number of deployments to disperse among APEX hosts via nightly cron
$SETTINGS['apex']['deployment_total'] = 300;

// the number of exported files to retreive among APEX hosts via nightly cron
$SETTINGS['apex']['export_total'] = 200;
