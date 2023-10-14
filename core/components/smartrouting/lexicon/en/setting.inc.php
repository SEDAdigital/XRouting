<?php
/**
 * Settings Lexicon Entries for SmartRouting
 *
 * @package smartrouting
 * @subpackage lexicon
 */
$_lang['setting_smartrouting.allow_debug_info'] = 'Allow debug output';
$_lang['setting_smartrouting.allow_debug_info_desc'] = 'Enable this setting to output debug info if the &smartrouting-debug=1 GET parameter is set. ATTENTION: disable it again after you debugged your installation, since this exposes a lot information of project!';
$_lang['setting_smartrouting.default_context'] = 'Default context';
$_lang['setting_smartrouting.default_context_desc'] = 'The default context to redirect to if no matches were found and smartrouting.show_no_match_error is set to No.';
$_lang['setting_smartrouting.include_www'] = 'Include WWW-subdomain';
$_lang['setting_smartrouting.include_www_desc'] = 'Specifies if the www-subdomain should automatically be included when matching against the base domain, ie. www.example.com should return the same context as example.com.';
$_lang['setting_smartrouting.show_no_match_error'] = 'Return error messages';
$_lang['setting_smartrouting.show_no_match_error_desc'] = 'If set to yes, SmartRouting will return an error instead of redirecting to the default context.';
