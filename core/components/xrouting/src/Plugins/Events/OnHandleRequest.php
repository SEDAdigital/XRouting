<?php
/**
 * @package xrouting
 * @subpackage plugin
 */

namespace SEDAdigital\XRouting\Plugins\Events;

use SEDAdigital\XRouting\Plugins\Plugin;

class OnHandleRequest extends Plugin
{
    private $debugInfo = [];

    /**
     * Initialize the plugin event.
     *
     * @return bool
     */
    public function init()
    {
        if ($this->modx->context->get('key') == 'mgr') {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @return mixed|void
     */
    public function process()
    {
        $contexts = $this->modx->cacheManager->get($this->xrouting->getOption('cacheKey'), $this->xrouting->getOption('cacheOptions'));
        if (empty($contexts)) {
            $contexts = $this->xrouting->buildContextArray();
            $this->modx->cacheManager->set($this->xrouting->getOption('cacheKey'), $contexts, 0, $this->xrouting->getOption('cacheOptions'));
        }

        if (!empty($contexts)) {
            $http_host = $_SERVER['HTTP_HOST'] ?? '';
            if ($this->xrouting->getOption('include_www')) {
                $http_host = str_replace('www.', '', $http_host);
            }

            // When the web context uses a base_url setting that differs from '/' calling
            // $modx->getOption('base_url', null, MODX_BASE_URL); can't be used, because it
            // uses the context settings from the already initialized context (web, in index.php).
            $baseUrlSetting = $this->modx->getObject('modSystemSetting', 'base_url');
            $modxBaseUrl = ($baseUrlSetting) ? $baseUrlSetting->get('value') : MODX_BASE_URL;

            $requestUrl = $_REQUEST[$this->modx->getOption('request_param_alias', null, 'q')] ?? '';
            $requestBaseUrl = str_replace('//', '/', $modxBaseUrl . $requestUrl);

            // find matching hosts
            $matches = [];
            $matched_contexts = $contexts['_hosts'][$http_host] ?? '';
            foreach ((array)$matched_contexts as $ckey) {
                $strpos = strpos($requestBaseUrl, $contexts[$ckey]['base_url']);
                if ($strpos === 0) {
                    // the longest (and first) base_url context setting will win the matches
                    $matches[strlen($contexts[$ckey]['base_url'])] = $ckey;
                }
            }

            $xroutingDebug = $this->xrouting->getBooleanOption('xrouting-debug', $_REQUEST, false);

            // modify request for the matched context
            if (!empty($matches)) {
                $cSettings = $contexts[$matches[max(array_keys($matches))]];
                $cKey = $matches[max(array_keys($matches))];

                // do we need to switch the context?
                if ($this->modx->context->get('key') != $cKey) {
                    $this->switchContext($cKey);
                }

                // remove base_url from request query
                if ($cSettings['base_url'] != $modxBaseUrl) {
                    $newRequestUrl = str_replace($cSettings['base_url'], '', $requestBaseUrl);
                    $_REQUEST[$this->modx->getOption('request_param_alias', null, 'q')] = $newRequestUrl;
                }
            } elseif (!$xroutingDebug || $this->xrouting->getOption('allow_debug_info')) {
                // if no match found
                if ($this->xrouting->getOption('show_no_match_error')) {
                    $this->modx->sendErrorPage();
                } else {
                    $this->modx->switchContext($this->xrouting->getOption('default_context'));
                }
            }

            // output debug info
            if ($xroutingDebug && $this->xrouting->getOption('allow_debug_info')) {
                $this->debug('MODX context map', print_r($contexts, true));
                $this->debug('Requested URL', $_REQUEST[$this->modx->getOption('request_param_alias', null, 'q')]);
                $this->debug('Requested URL with base_url', $requestBaseUrl);
                $this->debug('Matched context(s) (Array key defines match quality)', print_r($matches, true));
                $this->debug('Request will go to context', !empty($matches) ? $matches[max(array_keys($matches))] : '');
                $this->debug('Modified request URL', $newRequestUrl ?? '');
                $this->debug('The used cultureKey', $this->modx->cultureKey);
                @session_write_close();
                die('<pre>' . implode("\n\n\n", $this->debugInfo) . '</pre>');
            }
        }
    }

    /**
     * @param $section
     * @param $message
     * @return void
     */
    private function debug($section, $message)
    {
        $this->debugInfo[] = "## $section: \n\n$message";
    }

    /**
     * Switch the context, set the cultureKey and the locale
     *
     * @param $contextKey
     * @return void
     */
    private function switchContext($contextKey)
    {
        $this->modx->switchContext($contextKey);

        // get culture key from REQUEST, SESSION or system setting
        $cultureKey = $this->modx->getOption('cultureKey', null, 'en');
        $cultureKey = $this->modx->getOption('cultureKey', $_SESSION, $cultureKey, true);
        $cultureKey = $this->modx->getOption('cultureKey', $_REQUEST, $cultureKey, true);
        $this->modx->cultureKey = $cultureKey;
        $this->modx->setOption('cultureKey', $cultureKey);

        // set locale since $this->modx->_initCulture is called before OnHandleRequest
        if ($this->modx->getOption('setlocale', null, true)) {
            $locale = setlocale(LC_ALL, null);
            setlocale(LC_ALL, $this->modx->getOption('locale', null, $locale, true));
        }
    }
}
