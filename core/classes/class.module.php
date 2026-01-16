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
        $this->bearsamppConfRaw = @parse_ini_file($this->bearsamppConf);

        if ($bearsamppRoot->isRoot()) {
            $this->createSymlink();
        }
    }

    /**
     * Creates a symbolic link from the current path to the symlink path.
     * If the symlink already exists and points to the correct target, no action is taken.
     */
    private function createSymlink()
    {
        $src = Util::formatWindowsPath($this->currentPath);
        $dest = Util::formatWindowsPath($this->symlinkPath);

        if(file_exists($dest)) {
            if (is_link($dest)) {
                $target = readlink($dest);
                if ($target == $src) {
                    return;
                }
                Batch::removeSymlink($dest);
            } elseif (is_file($dest)) {
                Util::logError('Removing . ' . $this->symlinkPath . ' file. It should not be a regular file');
                unlink($dest);
            } elseif (is_dir($dest)) {
                if (!(new \FilesystemIterator($dest))->valid()) {
                    rmdir($dest);
                } else {
                    Util::logError($this->symlinkPath . ' should be a symlink to ' . $this->currentPath . '. Please remove this dir and restart bearsampp.');
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
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');
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
}
