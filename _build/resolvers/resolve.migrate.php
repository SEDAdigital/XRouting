<?php
/**
 * Resolve migrate XRouting
 *
 * @package smartrouting
 * @subpackage build
 *
 * @var array $options
 * @var xPDOObject $object
 */

$success = false;
$migrateSettings = [
    'include_www',
    'default_context',
    'show_no_match_error',
    'allow_debug_info'
];

if ($object->xpdo) {
    if (!function_exists('migrateSettings')) {
        /**
         * @param xPDO $modx
         * @param $settingKeys
         * @return bool
         */
        function migrateSettings($modx, $settingKeys)
        {
            foreach ($settingKeys as $settingKey) {
                /** @var modSystemSetting $oldSetting */
                $oldSetting = $modx->getObject('modSystemSetting', [
                    'key' => 'xrouting.' . $settingKey
                ]);
                if ($oldSetting) {
                    /** @var modSystemSetting $newSetting */
                    $newSetting = $modx->getObject('modSystemSetting', [
                        'key' => 'smartrouting.' . $settingKey
                    ]);
                    if ($newSetting && $newSetting->get($settingKey) != $oldSetting->get($settingKey)) {
                        $newSetting->set($settingKey, $oldSetting->get($settingKey));
                        if ($newSetting->save()) {
                            $modx->log(xPDO::LOG_LEVEL_INFO, 'Migrated xrouting.' . $settingKey . ' setting to smartrouting. ' . $settingKey . ' setting.');
                        } else {
                            $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not migrate xrouting.' . $settingKey . ' setting to smartrouting. ' . $settingKey . ' setting.');
                        }
                    }
                }
            }
        }
    }

    if (!function_exists('disableXRouting')) {
        /**
         * @param xPDO $modx
         * @param $settingKeys
         * @return bool
         */
        function disableXRouting($modx)
        {
            /** @var modPlugin $oldPlugin */
            $oldPlugin = $modx->getObject('modPlugin', [
                'name' => 'XRouting'
            ]);
            if ($oldPlugin) {
                $oldPlugin->set('disabled', true);
                if ($oldPlugin->save()) {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'XRouting plugin disabled. You can uninstall XRouting now.');
                } else {
                    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not disable the XRouting plugin.');
                }
            }
        }
    }

    /** @var xPDO $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            migrateSettings($modx, $migrateSettings);
            disableXRouting($modx);
            $success = true;
            break;
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }
}
return $success;
