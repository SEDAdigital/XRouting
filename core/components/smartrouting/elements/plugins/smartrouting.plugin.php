<?php
/**
 * SmartRouting Plugin
 *
 * @package smartrouting
 * @subpackage plugin
 *
 * @var modX $modx
 * @var array $scriptProperties
 */

$className = 'TreehillStudio\SmartRouting\Plugins\Events\\' . $modx->event->name;

$corePath = $modx->getOption('smartrouting.core_path', null, $modx->getOption('core_path') . 'components/smartrouting/');
/** @var SmartRouting $smartrouting */
$smartrouting = $modx->getService('smartrouting', 'SmartRouting', $corePath . 'model/smartrouting/', [
    'core_path' => $corePath
]);

if ($smartrouting) {
    if (class_exists($className)) {
        $handler = new $className($modx, $scriptProperties);
        if (get_class($handler) == $className) {
            $handler->run();
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' could not be initialized!', '', 'SmartRouting Plugin');
        }
    } else {
        $modx->log(xPDO::LOG_LEVEL_ERROR, $className. ' was not found!', '', 'SmartRouting Plugin');
    }
}

return;
