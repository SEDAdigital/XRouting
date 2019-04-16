<?php
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->fromArray(array(
    'id' => 1,
    'name' => 'XRouting',
    'description' => 'XRouting is a simple plugin that handles requests for different contexts. It automatically switches the context based on a (sub)domain AND/OR subfolder.',
    'plugincode' => getSnippetContent($sources['plugins'] . 'xrouting.plugin.php'),
),'',true,true);

$events = array();
$events['OnMODXInit']= $modx->newObject('modPluginEvent');
$events['OnMODXInit']->fromArray(array(
    'event' => 'OnMODXInit',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnContextSave']= $modx->newObject('modPluginEvent');
$events['OnContextSave']->fromArray(array(
    'event' => 'OnContextSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnContextRemove']= $modx->newObject('modPluginEvent');
$events['OnContextRemove']->fromArray(array(
    'event' => 'OnContextRemove',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);
$events['OnSiteRefresh']= $modx->newObject('modPluginEvent');
$events['OnSiteRefresh']->fromArray(array(
    'event' => 'OnSiteRefresh',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Added '.count($events).' Plugin Events for the XRouting plugin.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for XRouting!'); flush();
}
unset($events);

return $plugins;