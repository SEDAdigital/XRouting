<?php
/**
 * Abstract plugin
 *
 * @package smartrouting
 * @subpackage plugin
 */

namespace TreehillStudio\SmartRouting\Plugins;

use modX;
use SmartRouting;

/**
 * Class Plugin
 */
abstract class Plugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var SmartRouting $smartrouting */
    protected $smartrouting;
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
        $corePath = $this->modx->getOption('smartrouting.core_path', null, $this->modx->getOption('core_path') . 'components/smartrouting/');
        $this->smartrouting = $this->modx->getService('smartrouting', 'SmartRouting', $corePath . 'model/smartrouting/', [
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
