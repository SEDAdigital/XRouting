<?php
/**
 * xrouting
 *
 * Copyright 2014-2023 by Christian Seel - <office@seda.digital>
 *
 * @package xrouting
 * @subpackage classfile
 */

namespace SEDAdigital\XRouting;

use modX;
use xPDO;

/**
 * class XRouting
 */
class XRouting
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'xrouting';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'XRouting';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.5.0';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * XRouting constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('xrouting');

        // Add default options
        $this->options = array_merge($this->options, [
            'include_www' => $this->getBooleanOption('include_www', $options, true),
            'show_no_match_error' => $this->getBooleanOption('show_no_match_error', $options, true),
            'default_context' => $this->modx->getOption($this->namespace . '.default_context', $options, 'web'),
            'allow_debug_info' => $this->getBooleanOption('allow_debug_info', $options, false),
            'cacheKey' => 'contextmap',
            'cacheOptions' => [
                xPDO::OPT_CACHE_KEY => $this->namespace,
                xPDO::OPT_CACHE_HANDLER => $this->modx->getOption('cache_resource_handler', null, $this->modx->getOption(xPDO::OPT_CACHE_HANDLER, null, 'xPDOFileCache')),
            ],
        ]);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get Boolean Option
     *
     * @param string $key
     * @param array $options
     * @param mixed $default
     * @return bool
     */
    public function getBooleanOption($key, $options = [], $default = null)
    {
        $option = $this->getOption($key, $options, $default);
        return ($option === 'true' || $option === true || $option === '1' || $option === 1);
    }

    public function buildContextArray() {
        $contexts = [];

        $query = $this->modx->newQuery('modContext');
        $query->where(['modContext.key:NOT IN' => ['mgr']]);
        $query->sortby($this->modx->escape('modContext') . '.' . $this->modx->escape('key'), 'DESC');
        $contextsGraph = $this->modx->getCollectionGraph('modContext', '{"ContextSettings":{}}', $query);

        foreach ($contextsGraph as $context) {
            $contextSettings = [];
            foreach ($context->ContextSettings as $cSetting) {
                $contextSettings[$cSetting->get('key')] = $cSetting->get('value');
            }
            if (!empty($contextSettings['http_host']) && !empty($contextSettings['base_url'])) {
                // add http_host to hosts list
                $contexts['_hosts'][$contextSettings['http_host']][] = $context->get('key');
                // add alias hosts to host list
                if (!empty($contextSettings['http_host_aliases'])) {
                    foreach (explode(',', $contextSettings['http_host_aliases']) as $alias) {
                        $contexts['_hosts'][$alias][] = $context->get('key');
                    }
                }
                // add context settings
                $contexts[$context->get('key')] = $contextSettings;
            }
        }

        return $contexts;
    }
}
