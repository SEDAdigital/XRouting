<?php
/**
 * XRouting
 * A MODX context routing extra
 *
 * @version   2.0.0
 * @package   xrouting
 * @author    Christian Seel <cs@seda.digital>
 * @copyright 2017 SEDA.digital GmbH & Co. KG
 */

$modx->getService('xrouting', 'XRouting', MODX_CORE_PATH . 'components/xrouting/model/', array());
if (!$modx->xrouting instanceof XRouting) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[XRouting] Could not load XRouting service from ' . $xrouting_path);
    $errorMessage = 'Site temporarily unavailable. Routing Error.';
    @include(MODX_CORE_PATH . 'error/unavailable.include.php');
    header('HTTP/1.1 503 Service Unavailable');
    echo "<html><title>Error 503: Site temporarily unavailable</title><body><h1>Error 503</h1><p>{$errorMessage}</p></body></html>";
    exit();
}

switch ($modx->event->name) {

    // "refresh cache" part
    case 'OnContextSave':
    case 'OnContextRemove':
    case 'OnSiteRefresh':

        // regenerate the context map for future routing
        $modx->xrouting->generateContextMap();

    break;
    
    
    // "routing" part
    default:
    case 'OnHandleRequest':
        if ($modx->context->get('key') == 'mgr') return;

        // get target context from class
        $cKey = $modx->xrouting->getContext();

        // do we need to switch the context?
        if ($modx->context->get('key') != $cKey) {
            $modx->switchContext($cKey);

            // update cultureKey everywhere
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
        }

    break;
}
