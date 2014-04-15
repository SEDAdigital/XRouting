<?php
$settings = array();

$settings['xrouting.include_www']= $modx->newObject('modSystemSetting');
$settings['xrouting.include_www']->fromArray(array(
    'key' => 'xrouting.include_www',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'xrouting',
    'area' => 'common',
),'',true,true);

$settings['xrouting.default_context']= $modx->newObject('modSystemSetting');
$settings['xrouting.default_context']->fromArray(array(
    'key' => 'xrouting.default_context',
    'value' => 'web',
    'xtype' => 'textfield',
    'namespace' => 'xrouting',
    'area' => 'common',
),'',true,true);

$settings['xrouting.show_no_match_error']= $modx->newObject('modSystemSetting');
$settings['xrouting.show_no_match_error']->fromArray(array(
    'key' => 'xrouting.show_no_match_error',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'xrouting',
    'area' => 'common',
),'',true,true);

return $settings;