<?php
/**
 * XRouting Plugin
 *
 * @package xrouting
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'SEDAdigital\XRouting\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('xrouting.core_path', null, $modx->getOption('core_path') . 'components/xrouting/');
/** @var XRouting $xrouting */
$xrouting = $modx->getService('xrouting', 'XRouting', $corePath . 'model/xrouting/', [
    'core_path' => $corePath
]);

if ($xrouting) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'XRouting Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'XRouting Plugin');
    }
}

return;
