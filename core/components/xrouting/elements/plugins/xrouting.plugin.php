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
                    foreach (explode(',', $contextSettings['http_host_aliases']) as $alias) {
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
        if ($modx->context->get('key') == 'mgr')
            return;

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
                        foreach (explode(',', $contextSettings['http_host_aliases']) as $alias) {
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
                $http_host = str_replace('www.', '', $http_host);
            }
            $requestUrl = '/' . rtrim($_REQUEST[$modx->getOption('request_param_alias', null, 'q')], '/') . '/';
            $matches = array();


            // find matching hosts
            $matched_contexts = $contexts['_hosts'][$http_host];


            foreach ($matched_contexts as $index => $ckey) {

                $context = $contexts[$ckey];

                $strpos = strpos($requestUrl, $contexts[$ckey]['base_url']);

                if ($strpos === 0) {
                    $matches[strlen($contexts[$ckey]['base_url'])] = $ckey;
                }
            }


            if (!empty($matches)) {

                $cSettings = $contexts[$matches[max(array_keys($matches))]];
                $cKey = $matches[max(array_keys($matches))];

                //die($cKey);

                //get current settings, which shouldn't change after context-switch
                $settings_to_reset = explode(',',$modx->getOption('xrouting.reset_after_contextswitch_settings'));
                $reset_settings = array();
                if (!empty($settings_to_reset)){
                    foreach ($settings_to_reset as $setting){
                        $reset_settings[$setting] = $modx->getOption($setting);
                    }
                }
                
                //$mml_settings = array('cultureKey' => $modx->getOption('cultureKey'), 'site_url' => $modx->getOption('site_url'));

                // do we need to switch the context?
                if ($modx->context->get('key') != $cKey) {
                    $modx->switchContext($cKey);
                }

                //reset settings
                if (!empty($reset_settings));
                
                $modx->setPlaceholders($reset_settings, '+');
                foreach ($reset_settings as $key => $value) {
                    $modx->setOption($key, $value);
                }

                // remove base_url from request query
                if ($cSettings['base_url'] != '/') {
                    $newRequestUrl = str_replace($cSettings['base_url'], '/', $requestUrl);
                    $_REQUEST[$modx->getOption('request_param_alias', null, 'q')] = $newRequestUrl;
                }

            } else {
                // if no match found
                if ($modx->getOption('xrouting.show_no_match_error', null, true)) {
                    $modx->sendErrorPage();
                } else {
                    $modx->switchContext($modx->getOption('xrouting.default_context', null, 'web'));
                }

            }
        }
        break;
}
