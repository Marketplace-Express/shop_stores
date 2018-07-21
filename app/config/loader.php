<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Shop_multivendor\Models' => APP_PATH . '/common/models/',
    'Shop_multivendor'        => APP_PATH . '/common/library/',
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'Shop_multivendor\Modules\Frontend\Module' => APP_PATH . '/modules/frontend/Module.php',
    'Shop_multivendor\Modules\Cli\Module'      => APP_PATH . '/modules/cli/Module.php'
]);

$loader->register();
