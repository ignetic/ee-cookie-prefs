<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = array(

	'ee_cookie_prefs_name' => 'EE Cookie Prefs',
	'ee_cookie_prefs_desc' => 'Override individual cookie settings with entered settings values',
	'ee_cookie_prefs_note' => 'Enables you to change EE cookie variables. Leave field empty to use default settings.<br>
		Enter cookie name including prefix.<br>
		See: <a href="'.ee('CP/URL', 'settings/security-privacy').'" target="_blank">Cookie Settings</a>',
	
	'ee_cookie_prefs_save_success' => 'Cookie Settings Saved',
	'ee_cookie_prefs_save_success_desc' => 'The cookie settings were saved successfully',
	'ee_cookie_prefs_save_empty' => 'No Cookie Settings Saved',
	'ee_cookie_prefs_save_empty_desc' => 'No overriding cookie settings have been saved',

	'name' => 'Name',
	'cookie_name' => 'Cookie Name',
	'expires' => 'Expires',
	'path' => 'Path',
	'domain' => 'Domain',
	'secure_cookie' => 'Secure',
	'httponly' => 'HttpOnly',
	'samesite' => 'SameSite',
	'delete' => 'Delete',
	'add_row' => 'Add Row',
	'example_cookie_name' => 'Example cookie name',
	
	'other_options' => 'Other Cookie Options',
	'other_options_desc' => 'Additional settings which may help with your cookie accessibility',	
	'cookie_consent_desc' => 'Client-side Cookie Consent options - enables access to consent cookie via javascript',
	
	'enable_consent_cookies' => 'Enable \'Cookie Consent\' client-side cookies',
	'enable_consent_cookies_desc' => 'Output \'Cookie Consent\' settings to client side cookies.<br>The Cookie Content module normally stores the preferences in the database or cookies inaccessible by javascript.<br>
		This will allow you to access this cookie via javascript; for example in cases when using static caching.<br>
		See: <a href="'.ee('CP/URL', 'settings/security-privacy').'" target="_blank">Cookie Consent Setting</a>',
	'enable_consent_cookies_name' => 'Cookie Consent cookie name',
	'enable_consent_cookies_name_desc' => 'When the above is enabled, store the visitor consent settings in different named cookie.<br>
		When left empty the default `visitor_consents` cookie will be overwritten.<br>
		* Requires no prefix.',
	
	'samesite_none_fix' => 'Enable SameSite=None cookie browser fix (recommended)',
	'samesite_none_fix_desc' => 'Not all browsers are compatible with SameSite=None.<br>
		Enable this if you experience issues where these cookies are lost when returning back to the site.<br>
		See: <a href="https://www.chromium.org/updates/same-site/incompatible-clients" target="_blank">https://www.chromium.org/updates/same-site/incompatible-clients</a>',
	
	
	'enable_consent_cookies_format' => 'Cookie Consent value format',	
	'enable_consent_cookies_format_desc' => 'If a cookie name is provided above, you may select a preferred format of the value held within the cookie',
	'comma_separated_values' => 'Comma Separated Values',
);

