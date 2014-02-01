<?php
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','XRouting');
$plugins[0]->set('description','XRouting is a simple plugin that handles requests for different contexts. It automatically switches the context based on a (sub)domain AND/OR subfolder.');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'xrouting.plugin.php'));
$plugins[0]->set('category', 0);

$events = array();
$events['OnHandleRequest']= $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray(array(
    'event' => 'OnHandleRequest',
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
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for XRouting.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for XRouting!');
}
unset($events);

return $plugins;


