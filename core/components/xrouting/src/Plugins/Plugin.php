<?php
/**
 * Abstract plugin
 *
 * @package xrouting
 * @subpackage plugin
 */

namespace SEDAdigital\XRouting\Plugins;

use modX;
use XRouting;

/**
 * Class Plugin
 */
abstract class Plugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var XRouting $xrouting */
    protected $xrouting;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    /**
     * Plugin constructor.
     *
     * @param $modx
     * @param $scriptProperties
     */
    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx =& $modx;
        $corePath = $this->modx->getOption('xrouting.core_path', null, $this->modx->getOption('core_path') . 'components/xrouting/');
        $this->xrouting = $this->modx->getService('xrouting', 'XRouting', $corePath . 'model/xrouting/', [
            'core_path' => $corePath
        ]);
    }

    /**
     * Run the plugin event.
     */
    public function run()
    {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    /**
     * Initialize the plugin event.
     *
     * @return bool
     */
    public function init()
    {
        return true;
    }

    /**
     * Process the plugin event code.
     *
     * @return mixed
     */
    abstract public function process();
}
