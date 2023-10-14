<?php
/**
 * Setup options
 *
 * @package smartrouting
 * @subpackage build
 *
 * @var array $options
 */

$output = '<style type="text/css">
    #modx-setupoptions-panel { display: none; }
    #modx-setupoptions-form p { margin-bottom: 10px; }
    #modx-setupoptions-form h2 { margin-bottom: 15px; }
</style>';

$values = [];
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $output .= '<h2>Install SmartRouting</h2>

        <p>Thanks for installing SmartRouting. This open source extra is
        developed by Treehill Studio - MODX development in Münsterland. It
        bases on the code of XRouting created by Christian Seel.</p>

        <p>During the installation, we will collect some statistical data (the
        hostname, the IP address, the PHP version and the MODX version of your
        MODX installation). Your data will be kept confidential and under no
        circumstances be used for promotional purposes or disclosed to third
        parties. We only like to know the usage count of this package.</p>
        
        <p>If you install this package, you are giving us your permission to
        collect, process and use that data for statistical purposes.</p>';

        break;
    case xPDOTransport::ACTION_UPGRADE:
        $output .= '<h2>Upgrade SmartRouting</h2>

        <p>SmartRouting will be upgraded. This open source extra is
        developed by Treehill Studio - MODX development in Münsterland. It
        bases on the code of XRouting created by Christian Seel.</p>

        During the upgrade, we will collect some statistical data (the hostname,
        the IP address, the PHP version, the MODX version of your MODX
        installation and the previous installed version of this extra package).
        Your data will be kept confidential and under no circumstances be used
        for promotional purposes or disclosed to third parties. We only like to
        know the usage count of this package.</p>

        <p>If you upgrade this package, you are giving us your permission to
        collect, process and use that data for statistical purposes.</p>';

        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

return $output;
