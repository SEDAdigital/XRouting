<?php
/**
 * @package xrouting
 * @subpackage plugin
 */

namespace SEDAdigital\XRouting\Plugins\Events;

use SEDAdigital\XRouting\Plugins\Plugin;

class OnSiteRefresh extends Plugin
{
    public function process()
    {
        $contexts = $this->xrouting->buildContextArray();
        $this->modx->cacheManager->set($this->xrouting->getOption('cacheKey'), $contexts, 0, $this->xrouting->getOption('cacheOptions'));
    }
}
