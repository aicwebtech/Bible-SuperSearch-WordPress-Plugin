<?php

defined('ABSPATH') or die; // exit if accessed directly

/**
 * Simple PSR-4 autoloader for BibleSuperSearch namespaces.
 */
class BibleSuperSearch_Autoloader
{
    /**
     * Map of namespace prefixes to base directories.
     *
     * @var array<string, string>
     */
    private $prefixes = [];

    /**
     * Register a namespace prefix with a base directory.
     */
    public function addNamespace(string $prefix, string $baseDir): void
    {
        // Normalize namespace prefix with trailing separator
        $prefix = trim($prefix, '\\') . '\\';

        // Normalize base directory with trailing separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->prefixes[$prefix] = $baseDir;
    }

    /**
     * Register this autoloader with SPL.
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Load the class file for a given class name.
     */
    public function loadClass(string $class): void
    {
        foreach ($this->prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);

            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
            }

            return;
        }
    }
}
