<?php
switch ($modx->event->name) {

    // "refresh cache" part
    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':
        
        $contexts = array();
        $cacheKey = 'xrouting_contextmap';
        $cacheOptions = array();
        
        // build context array
        $query = $modx->newQuery('modContext');
        $query->where(array('modContext.key:NOT IN' => array('mgr')));
        $query->sortby($modx->escape('modContext') . '.' . $modx->escape('key'), 'DESC');
        $contextsGraph = $modx->getCollectionGraph('modContext', '{"ContextSettings":{}}', $query);
        
        foreach ($contextsGraph as $context) {
            $contextSettings = array();
            foreach ($context->ContextSettings as $cSetting) {
                $contextSettings[$cSetting->get('key')] = $cSetting->get('value');
            }
            
            if (!empty($contextSettings['http_host']) && !empty($contextSettings['base_url'])) {
                
                // add http_host to hosts list
                $contexts['_hosts'][$contextSettings['http_host']][] = $context->get('key');
                
                // add alias hosts to host list
                if (!empty($contextSettings['http_host_aliases'])) {
                    foreach (explode(',',$contextSettings['http_host_aliases']) as $alias) {
                        $contexts['_hosts'][$alias][] = $context->get('key');
                    }
                }
                
                // add context settings
                $contexts[$context->get('key')] = $contextSettings;
            }
        }
         
        unset($contextsGraph);
        $modx->cacheManager->set($cacheKey, $contexts, 0, $cacheOptions);
    break;
    
    
    // "routing" part
    default:
    case 'OnHandleRequest':
        if ($modx->context->get('key') == 'mgr') return;
            
        $contexts = array();
        
        $cacheKey = 'xrouting_contextmap';
        $cacheOptions = array();
        $contexts = $modx->cacheManager->get($cacheKey, $cacheOptions);
        
        if (empty($contexts)) {
        // build context array
            $query = $modx->newQuery('modContext');
            $query->where(array('modContext.key:NOT IN' => array('mgr')));
            $query->sortby($modx->escape('modContext') . '.' . $modx->escape('key'), 'DESC');
            $contextsGraph = $modx->getCollectionGraph('modContext', '{"ContextSettings":{}}', $query);
            
            foreach ($contextsGraph as $context) {
                $contextSettings = array();
                foreach ($context->ContextSettings as $cSetting) {
                    $contextSettings[$cSetting->get('key')] = $cSetting->get('value');
                }
                
                if (!empty($contextSettings['http_host']) && !empty($contextSettings['base_url'])) {
                    
                    // add http_host to hosts list
                    $contexts['_hosts'][$contextSettings['http_host']][] = $context->get('key');
                    
                    // add alias hosts to host list
                    if (!empty($contextSettings['http_host_aliases'])) {
                        foreach (explode(',',$contextSettings['http_host_aliases']) as $alias) {
                            $contexts['_hosts'][$alias][] = $context->get('key');
                        }
                    }
                    
                    // add context settings
                    $contexts[$context->get('key')] = $contextSettings;
                }
            }
                         
            unset($contextsGraph);
            $modx->cacheManager->set($cacheKey, $contexts, 0, $cacheOptions);
        }


        if (!empty($contexts)) {
            $http_host = $_SERVER['HTTP_HOST'];
            if ($modx->getOption('xrouting.include_www', null, true)) {
                $http_host = str_replace('www.','',$http_host);
            }
            
            $modx_base_url = false;
            $baseUrlSetting = $modx->getObject('modSystemSetting', 'base_url');
            if ($baseUrlSetting) $modx_base_url = $baseUrlSetting->get('value');
            if (!$modx_base_url) $modx_base_url = MODX_BASE_URL;
            
            $requestUrl = str_replace('//','/',$modx_base_url.$_REQUEST[$modx->getOption('request_param_alias', null, 'q')]);
            $matches = array();
            
            
        // find matching hosts
            $matched_contexts = $contexts['_hosts'][$http_host];
            
            
            foreach ((array) $matched_contexts as $index => $ckey) {
                
                $context = $contexts[$ckey];
                $strpos = strpos($requestUrl, $contexts[$ckey]['base_url']);
                if ($strpos === 0) {
                    $matches[strlen($contexts[$ckey]['base_url'])] = $ckey;
                }
            }

        // modify request for the matched context
            if (!empty($matches)) {
                
                $cSettings = $contexts[$matches[max(array_keys($matches))]];
                $cKey = $matches[max(array_keys($matches))];
                
                // do we need to switch the context?
                if ($modx->context->get('key') != $cKey) {
                    $modx->switchContext($cKey);
                    
                    $cultureKey = $modx->getOption('cultureKey', null, 'en');
                    if (!empty($_SESSION['cultureKey'])) $cultureKey = $_SESSION['cultureKey'];
                    if (!empty($_REQUEST['cultureKey'])) $cultureKey = $_REQUEST['cultureKey'];
                    $modx->cultureKey = $cultureKey;
                    $modx->setOption('cultureKey', $cultureKey);
        
                    // set locale since $modx->_initCulture is called before OnHandleRequest
                    if ($modx->getOption('setlocale', null, true)) {
                        $locale = setlocale(LC_ALL, null);
                        setlocale(LC_ALL, $modx->getOption('locale', null, $locale, true));
                    }
                    
                    // init the session, if it is disabled by anonymous_sessions = 0
                    if ($modx->getOption('anonymous_sessions', null, true) && !isset($_COOKIE[session_name()])) {
                        if (!$modx->startSession()) {
                            $modx->log(modX::LOG_LEVEL_ERROR, 'Unable to initialize a session', '', __METHOD__, __FILE__, __LINE__);
                            $modx->getUser($contextKey);
                            return;
                        }
                        $modx->getUser($contextKey);
                        $cookieExpiration = 0;
                        if (isset ($_SESSION['modx.' . $contextKey . '.session.cookie.lifetime'])) {
                            $cookieDomain = $modx->getOption('session_cookie_domain', $options, '');
                            $cookiePath = $modx->getOption('session_cookie_path', $options, MODX_BASE_URL);
                            $cookieSecure = (boolean)$modx->getOption('session_cookie_secure', $options, false);
                            $cookieHttpOnly = (boolean)$modx->getOption('session_cookie_httponly', $options, true);
                            $cookieLifetime = (integer)$modx->getOption('session_cookie_lifetime', $options, 0);
                            $sessionCookieLifetime = (integer)$_SESSION['modx.' . $contextKey . '.session.cookie.lifetime'];
                            if ($sessionCookieLifetime !== $cookieLifetime) {
                                if ($sessionCookieLifetime) {
                                    $cookieExpiration = time() + $sessionCookieLifetime;
                                }
                                setcookie(session_name(), session_id(), $cookieExpiration, $cookiePath, $cookieDomain,
                                    $cookieSecure, $cookieHttpOnly);
                            }
                        }
                    }
                }
                
                // remove base_url from request query
                if ($cSettings['base_url'] != $modx_base_url) {
                    $newRequestUrl = str_replace($cSettings['base_url'],'',$requestUrl);
                    $_REQUEST[$modx->getOption('request_param_alias', null, 'q')] = $newRequestUrl;
                }
                
                
            } else if ($_REQUEST['xrouting-debug'] != '1' || !$modx->getOption('xrouting.allow_debug_info', null, false)) {
                // if no match found
                if ($modx->getOption('xrouting.show_no_match_error', null, true)) {
                    $modx->sendErrorPage();
                } else {
                    $modx->switchContext($modx->getOption('xrouting.default_context', null, 'web'));
                }
                
            }
        
        // output debug info
            if (isset($_REQUEST['xrouting-debug']) && $_REQUEST['xrouting-debug'] == '1' && $modx->getOption('xrouting.allow_debug_info', null, false)) {
                $debuginfo = '<pre>';
                $debuginfo .= "## MODX context map:\n\n" . print_r($contexts,true) . "\n\n\n";
                $debuginfo .= "## Requested URL: " . $_REQUEST[$modx->getOption('request_param_alias', null, 'q')] . "\n\n\n";
                $debuginfo .= "## Requested URL with base_url: ". $requestUrl ."\n\n\n";
                $debuginfo .= "## Matched context(s) (Array key defines match quality):\n\n" . print_r($matches,true) . "\n\n\n";
                $debuginfo .= "## Request will go to context: " . $matches[@max(array_keys($matches))] . "\n\n\n";
                $debuginfo .= "## Modified request URL: " . $newRequestUrl . "\n\n\n";
                die($debuginfo);
            }
        }
    break;
}
