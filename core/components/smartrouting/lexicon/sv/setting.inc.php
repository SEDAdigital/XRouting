<?php
/**
 * Settings Lexicon Entries for SmartRouting
 *
 * @package smartrouting
 * @subpackage lexicon
 */
$_lang['setting_smartrouting.allow_debug_info'] = "Tillåt felsökningsutdata";
$_lang['setting_smartrouting.allow_debug_info_desc'] = "Aktivera den här inställningen för att ge ut felsökningsinformation om parametern &smartrouting-debug=1 GET är inställd. UPPMÄRKSAMHET: inaktivera den igen efter att du har felsökt din installation, eftersom detta avslöjar mycket information om projektet!";
$_lang['setting_smartrouting.default_context'] = 'Standardkontext';
$_lang['setting_smartrouting.default_context_desc'] = 'Den kontext man skall omdirigeras till om inga matchningar hittades och smartrouting.show_no_match_error är satt till Nej.';
$_lang['setting_smartrouting.include_www'] = 'Inkludera WWW-subdomän';
$_lang['setting_smartrouting.include_www_desc'] = 'Anger om www-subdomänen automatiskt skall inkluderas vid matchning emot basdomänen, ex. www.example.com skall returnera samma kontext som example.com.';
$_lang['setting_smartrouting.show_no_match_error'] = 'Returnera felmeddelanden';
$_lang['setting_smartrouting.show_no_match_error_desc'] = 'Om satt till ja, kommer SmartRouting att returnera ett felmeddelande istället för att omdirigera till standardkontexten.';
