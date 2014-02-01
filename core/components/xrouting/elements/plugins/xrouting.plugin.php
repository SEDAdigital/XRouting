<?php
switch ($modx->event->name) {

    // "refresh cache" part
    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':
        $cacheKey = 'context_map';
        $cacheOptions = array(
            xPDO::OPT_CACHE_HANDLER => $modx->getOption(xPDO::OPT_CACHE_HANDLER),
        );
        
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
            $contexts[$context->get('key')] = $contextSettings;
        }
        
        unset($contextsGraph);
        $modx->cacheManager->set($cacheKey, $contexts, 0, $cacheOptions);
    break;
    
    
    // "routing" part
    default:
    case 'OnHandleRequest':
        if ($modx->context->get('key') !== 'mgr') {
            
            $contexts = array();
            
            $cacheKey = 'context_map';
            $cacheOptions = array(
                xPDO::OPT_CACHE_HANDLER => $modx->getOption(xPDO::OPT_CACHE_HANDLER),
            );
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
                    $contexts[$context->get('key')] = $contextSettings;
                }
                
                unset($contextsGraph);
                $modx->cacheManager->set($cacheKey, $contexts, 0, $cacheOptions);
            }
            
            if (!empty($contexts)) {
                $requestUrl = $_SERVER['HTTP_HOST'] . rtrim($_SERVER['REQUEST_URI'],'/').'/';
                $found = false;
                
                foreach ($contexts as $cKey => $cSettings) {
                    
                    $strpos = strpos($requestUrl, $cSettings['http_host'] . $cSettings['base_url']);
                    if ($strpos === 0 && !empty($cSettings['http_host']) && !empty($cSettings['base_url'])) {
                        // found a match
                        $found = true;
                        
                        // do we need to switch the context?
                        if ($modx->context->get('key') != $cKey) {
                            $modx->switchContext($cKey);
                        }
                        
                        // remove base_url from request query
                        if ($cSettings['base_url'] != '/') {
                            $pieces = explode('/', trim($_REQUEST[$modx->getOption('request_param_alias', null, 'q')], ' '), 2);
                            $_REQUEST[$modx->getOption('request_param_alias', null, 'q')] = $pieces[1];
                        }
                        break;
                    }
                }
                // if no match found
                if (!$found) $modx->sendErrorPage();
            }
        }
    break;
}