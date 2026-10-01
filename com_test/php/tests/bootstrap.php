<?php

/**
 * PHPUnit bootstrap for the platform-agnostic Bible SuperSearch PHP library.
 *
 * Loads the library under test and registers a small PSR-4 autoloader for the
 * test namespace, mirroring the plugin's own hand-rolled autoloader so that no
 * package manager is required.
 */

$root = dirname(__DIR__);

require_once($root . '/init.php');
require_once($root . '/src/QueryStringParser.php');

spl_autoload_register(function ($class) use ($root) {
    $prefix = 'BibleSuperSearch\\Common\\Tests\\';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = $root . '/tests/' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require_once($file);
    }
});
