<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Abstract class representing a module in the Bearsampp application.
 * This class provides common functionalities for managing modules such as apps, bins, and tools.
 */
abstract class Module
{
    const BUNDLE_RELEASE = 'bundleRelease';

    private static $configCache = array();
    private static $skipSymlinkCreation = false;

    private $type;
    private $id;

    protected $name;
    protected $version;
    protected $release = 'N/A';

    protected $rootPath;
    protected $currentPath;
    protected $symlinkPath;
    protected $enable;
    protected $bearsamppConf;
    protected $bearsamppConfRaw;

    /**
     * Constructor for the Module class.
     * Initializes the module with default values.
     */
    protected function __construct() {
        // Initialization logic can be added here if needed
    }

    /**
     * Reloads the module configuration based on the provided ID and type.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    protected function reload($id = null, $type = null) {
        global $bearsamppRoot;

        $this->id = empty($id) ? $this->id : $id;
        $this->type = empty($type) ? $this->type : $type;
        $mainPath = 'N/A';

        switch ($this->type) {
            case Apps::TYPE:
                $mainPath = $bearsamppRoot->getAppsPath();
                break;
            case Bins::TYPE:
                $mainPath = $bearsamppRoot->getBinPath();
                break;
            case Tools::TYPE:
                $mainPath = $bearsamppRoot->getToolsPath();
                break;
        }

        $this->rootPath = $mainPath . '/' . $this->id;
        $this->currentPath = $this->rootPath . '/' . $this->id . $this->version;
        $this->symlinkPath = $this->rootPath . '/current';
        $this->enable = is_dir($this->currentPath);
        $this->bearsamppConf = $this->currentPath . '/bearsampp.conf';

        // Use disk cache (warm starts) + memory cache (within session)
        $cacheKey = md5($this->bearsamppConf);
        if (!isset(self::$configCache[$cacheKey])) {
            // CacheManager handles both disk cache and parsing
            $this->bearsamppConfRaw = CacheManager::load(
                $this->bearsamppConf,
                function($path) { return @parse_ini_file($path) ?: []; },
                $cacheKey
            );
            self::$configCache[$cacheKey] = $this->bearsamppConfRaw;
        } else {
            $this->bearsamppConfRaw = self::$configCache[$cacheKey];
        }

        if ($bearsamppRoot->isRoot() && !self::$skipSymlinkCreation) {
            $this->createSymlink();
        }
    }

    /**
     * Creates a symbolic link from the current path to the symlink path.
     * If the symlink already exists and points to the correct target, no action is taken.
     */
    private function createSymlink()
    {
        $src = Path::formatWindowsPath($this->currentPath);
        $dest = Path::formatWindowsPath($this->symlinkPath);

        if (is_link($dest)) {
            if (readlink($dest) === $src) {
                return;
            }
            Batch::removeSymlink($dest);
            Batch::createSymlink($src, $dest);
            return;
        }

        if (file_exists($dest)) {
            if (is_file($dest)) {
                Log::error('Removing . ' . $this->symlinkPath . ' file. It should not be a regular file');
                unlink($dest);
            } elseif (is_dir($dest)) {
                $it = new \FilesystemIterator($dest);
                if (!$it->valid()) {
                    rmdir($dest);
                } else {
                    Log::error($this->symlinkPath . ' should be a symlink to ' . $this->currentPath . '. Please remove this dir and restart bearsampp.');
                    return;
                }
            }
        }

        Batch::createSymlink($src, $dest);
    }

    /**
     * Replaces a specific key-value pair in the configuration file.
     *
     * @param string $key The key to replace.
     * @param string $value The new value for the key.
     */
    protected function replace($key, $value) {
        $this->replaceAll(array($key => $value));
    }

    /**
     * Replaces multiple key-value pairs in the configuration file.
     *
     * @param array $params An associative array of key-value pairs to replace.
     */
    protected function replaceAll($params) {
        $content = file_get_contents($this->bearsamppConf);

        foreach ($params as $key => $value) {
            $content = preg_replace('|' . $key . ' = .*|', $key . ' = ' . '"' . $value.'"', $content);
            $this->bearsamppConfRaw[$key] = $value;
        }

        file_put_contents($this->bearsamppConf, $content);

        // Invalidate both memory cache and disk cache
        $cacheKey = md5($this->bearsamppConf);
        unset(self::$configCache[$cacheKey]);
        CacheManager::invalidate($this->bearsamppConf);
    }

    /**
     * Updates the module configuration.
     *
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update process.
     */
    public function update($sub = 0, $showWindow = false) {
        $this->updateConfig(null, $sub, $showWindow);
    }

    /**
     * Updates the module configuration with a specific version.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update process.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        $version = $version == null ? $this->version : $version;
        Log::debug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');
    }

    /**
     * Returns the name of the module.
     *
     * @return string The name of the module.
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * Gets the type of the module.
     *
     * @return string The type of the module.
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Gets the ID of the module.
     *
     * @return string The ID of the module.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the name of the module.
     *
     * @return string The name of the module.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the version of the module.
     *
     * @return string The version of the module.
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Gets the list of available versions for the module.
     *
     * @return array The list of available versions.
     */
    public function getVersionList() {
        return Util::getVersionList($this->rootPath);
    }

    /**
     * Sets the version of the module.
     *
     * @param string $version The version to set.
     */
    abstract public function setVersion($version);

    /**
     * Gets the release information of the module.
     *
     * @return string The release information.
     */
    public function getRelease() {
        return $this->release;
    }

    /**
     * Gets the root path of the module.
     *
     * @return string The root path of the module.
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * Gets the current path of the module.
     *
     * @return string The current path of the module.
     */
    public function getCurrentPath() {
        return $this->currentPath;
    }

    /**
     * Gets the symlink path of the module.
     *
     * @return string The symlink path of the module.
     */
    public function getSymlinkPath() {
        return $this->symlinkPath;
    }

    /**
     * Checks if the module is enabled.
     *
     * @return bool True if the module is enabled, false otherwise.
     */
    public function isEnable() {
        return $this->enable;
    }

    /**
     * Skip symlink creation during module reload
     * Useful for performance optimization during service checking phase
     *
     * @param bool $skip True to skip symlink creation
     * @return void
     */
    public static function setSkipSymlinkCreation(bool $skip): void
    {
        self::$skipSymlinkCreation = $skip;
    }

    /**
     * Check if symlink creation is being skipped
     *
     * @return bool True if symlink creation is skipped
     */
    public static function isSkippingSymlinkCreation(): bool
    {
        return self::$skipSymlinkCreation;
    }
}

