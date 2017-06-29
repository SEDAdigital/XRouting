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

class XRouting
{

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->options = $options;
        $this->cacheOptions = array(xPDO::OPT_CACHE_KEY => 'xrouting');
        $this->cacheKey = 'contextmap';
    }

    public function getContext()
    {
        $cacheManager = $this->modx->getCacheManager();
        $contexts = $cacheManager->get($this->cacheKey, $this->cacheOptions);
        $cKey = $this->getOption('xrouting.default_context', null, 'web');

        if (empty($contexts)) {
            $contexts = $this->generateContextMap();
        }

        $http_host = $_SERVER['HTTP_HOST'];
        if ($this->getOption('xrouting.include_www', null, true)) {
            $http_host = str_replace('www.', '', $http_host);
        }

        $modx_base_url = $this->getOption('base_url', null, MODX_BASE_URL);
        $requestUrl = @$_REQUEST[$this->getOption('request_param_alias', null, 'q')];
        $requestUrl = str_replace('//', '/', $modx_base_url . $requestUrl);
        $matches = array();

        // find matching hosts
        $matched_contexts = @$contexts['_hosts'][$http_host];

        foreach ((array)$matched_contexts as $index => $ckey) {
            $strpos = strpos($requestUrl, $contexts[$ckey]['base_url']);
            if ($strpos === 0) {
                $matches[strlen($contexts[$ckey]['base_url'])] = $ckey;
            }
        }

        // modify request for the matched context
        if (!empty($matches)) {

            $cSettings = $contexts[$matches[max(array_keys($matches))]];
            $cKey = $matches[max(array_keys($matches))];

            $newRequestUrl = $requestUrl;
            // remove base_url from request query
            if ($cSettings['base_url'] != $modx_base_url) {
                $newRequestUrl = str_replace($cSettings['base_url'], '', $requestUrl);
                $_REQUEST[$this->getOption('request_param_alias', null, 'q')] = $newRequestUrl;
            }


        } else {
            if (!isset($_REQUEST['xrouting-debug']) || !$this->getOption('xrouting.allow_debug_info', null, false)) {
                // if no match found
                if ($this->getOption('xrouting.show_no_match_error', null, true)) {
                    $this->modx->initialize($this->getOption('xrouting.default_context', null, 'web'));
                    $this->modx->sendErrorPage();
                }
            } else {
                $this->modx->switchContext($this->getOption('xrouting.default_context', null, 'web'));
            }
        }

        // output debug info
        if (isset($_REQUEST['xrouting-debug']) && $this->getOption('xrouting.allow_debug_info', null, false)) {
            $debuginfo = '<pre>';
            $debuginfo .= "## Context map:\n\n" . print_r($contexts, true) . "\n\n\n";
            $debuginfo .= "## Request HTTP host: " . $_SERVER['HTTP_HOST'] . ' => ' . $http_host . "\n\n\n";
            $debuginfo .= "## Requested URL: " . $_REQUEST[$this->getOption('request_param_alias', null,
                    'q')] . "\n\n\n";
            $debuginfo .= "## Requested URL with base_url: " . $requestUrl . "\n\n\n";
            $debuginfo .= "## Matched context(s) (Array key defines match quality):\n\n" . print_r($matches,
                    true) . "\n\n\n";
            $debuginfo .= "## Request will go to context: " . $cKey . "\n\n\n";
            $debuginfo .= "## Modified request URL: " . $newRequestUrl;
            $debuginfo .= "</pre>";
            @session_write_close();
            die($debuginfo);
        }

        // return the matched context
        return $cKey;
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key     The option key to search for.
     * @param array  $options An array of options that override local options.
     * @param mixed  $default The default value returned if the option is not found locally or as a
     *                        namespaced system setting; by default this value is null.
     *
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists($key, $this->modx->config)) {
                $option = $this->modx->getOption($key);
            }
        }
        return $option;
    }

    public function generateContextMap()
    {
        // build context array while bypassing ACLs and querying only those context settings we actually need
        $query = $this->modx->newQuery('modContextSetting', array(
            'context_key:NOT IN' => array('mgr'),
            'key:IN' => array(
                'http_host',
                'http_host_aliases',
                'base_url',
                'site_url'
            )
        ));
        $query->select('context_key,key,value');
        $query->prepare();

        // execute while bypassing eventual ACLs
        if ($query->stmt->execute()) {
            $contextSettings = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $contextSettings = array();
        }

        $contexts = array('_hosts' => array());
        foreach ($contextSettings as $setting) {
            if (!isset($contexts[$setting['context_key']])) $contexts[$setting['context_key']] = array();
            $contexts[$setting['context_key']][$setting['key']] = $setting['value'];
        }

        foreach ($contexts as $cKey => $context) {
            if (!isset($context['http_host']) || empty($context['http_host'])) continue;

            // add http_host to hosts list
            $contexts['_hosts'][$context['http_host']][] = $cKey;

            // add alias hosts to host list
            if (!empty($context['http_host_aliases'])) {
                foreach (explode(',', $context['http_host_aliases']) as $alias) {
                    $contexts['_hosts'][$alias][] = $cKey;
                }
            }
        }

        $cacheManager = $this->modx->getCacheManager();
        $cacheManager->set($this->cacheKey, $contexts, 0, $this->cacheOptions);
        return $contexts;
    }

}