<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolGit
 *
 * This class represents the Git tool module in the Bearsampp application.
 * It handles the configuration, initialization, and management of Git-related settings and repositories.
 */
class ToolGit extends Module
{
    const ROOT_CFG_VERSION = 'gitVersion';

    const LOCAL_CFG_EXE = 'gitExe';
    const LOCAL_CFG_BASH = 'gitBash';
    const LOCAL_CFG_SCAN_STARTUP = 'gitScanStartup';

    const REPOS_FILE = 'repos.dat';
    const REPOS_CACHE_FILE = 'reposCache.dat';

    private $reposFile;
    private $reposCacheFile;
    private $repos;

    private $exe;
    private $bash;
    private $scanStartup;

    /**
     * Constructs a ToolGit object and initializes the Git tool module.
     *
     * @param string $id The ID of the module.
     * @param string $type The type of the module.
     */
    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    /**
     * Reloads the Git tool module configuration based on the provided ID and type.
     *
     * @param string|null $id The ID of the module. If null, the current ID is used.
     * @param string|null $type The type of the module. If null, the current type is used.
     */
    public function reload($id = null, $type = null) {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::GIT);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->reposFile = $this->symlinkPath . '/' . self::REPOS_FILE;
        $this->reposCacheFile = $this->symlinkPath . '/' . self::REPOS_CACHE_FILE;

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->bash = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_BASH];
            $this->scanStartup = $this->bearsamppConfRaw[self::LOCAL_CFG_SCAN_STARTUP];
        }

        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
        }
        if (!is_dir($this->symlinkPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->symlinkPath));
            return;
        }
        if (!is_file($this->bearsamppConf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->bearsamppConf));
        }
        if (!is_file($this->reposFile)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->reposFile));
        }
        if (!is_file($this->exe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->exe));
        }
        if (!is_file($this->bash)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->bash));
        }

        if (is_file($this->reposFile)) {
            $this->repos = explode(PHP_EOL, file_get_contents($this->reposFile));
            $rebuildRepos = array();
            foreach ($this->repos as $repo) {
                $repo = trim($repo);
                if (stripos($repo, ':') === false) {
                    $repo = $bearsamppRoot->getRootPath() . '/' . $repo;
                }
                if (is_dir($repo)) {
                    $rebuildRepos[] = Util::formatUnixPath($repo);
                } else {
                    Util::logWarning($this->name . ' repository not found: ' . $repo);
                }
            }
            $this->repos = $rebuildRepos;
        }
    }

    /**
     * Updates the Git tool module configuration with a specific version.
     *
     * @param string|null $version The version to update to. If null, the current version is used.
     * @param int $sub The sub-level for logging indentation.
     * @param bool $showWindow Whether to show a window during the update process.
     * @return bool True if the update was successful, false otherwise.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config');

        if (file_exists($this->getSymlinkPath() . '/post-install.bat')) {
            $bearsamppWinbinder->exec($this->getBash(), '--no-needs-console --hide --no-cd --command=' . $this->getSymlinkPath() . '/post-install.bat', true);
        }

        $bearsamppWinbinder->exec($this->getExe(), 'config --global core.autocrlf false', true);
        $bearsamppWinbinder->exec($this->getExe(), 'config --global core.eol lf', true);

        return true;
    }

    /**
     * Finds Git repositories either from cache or by scanning the directories.
     *
     * @param bool $cache Whether to use the cached repositories list.
     * @return array The list of found repositories.
     */
    public function findRepos($cache = true) {
        $result = array();

        if ($cache) {
            if (file_exists($this->reposCacheFile)) {
                $repos = file($this->reposCacheFile);
                foreach ($repos as $repo) {
                    array_push($result, trim($repo));
                }
            }
        } else {
            if (!empty($this->repos)) {
                foreach ($this->repos as $repo) {
                    $foundRepos = Util::findRepos($repo, $repo,'.git/config');
                    if (!empty($foundRepos)) {
                        foreach ($foundRepos as $foundRepo) {
                            array_push($result, $foundRepo);
                        }
                    }
                }
            }
            $strResult = implode(PHP_EOL, $result);
            file_put_contents($this->reposCacheFile, $strResult);
        }

        return $result;
    }

    /**
     * Sets the version of the Git tool module.
     *
     * @param string $version The version to set.
     */
    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    /**
     * Retrieves the list of repositories.
     *
     * @return array The list of repositories.
     */
    public function getRepos() {
        return $this->repos;
    }

    /**
     * Retrieves the path to the Git executable.
     *
     * @return string The path to the Git executable.
     */
    public function getExe() {
        return $this->exe;
    }

    /**
     * Retrieves the path to the Git Bash executable.
     *
     * @return string The path to the Git Bash executable.
     */
    public function getBash() {
        return $this->bash;
    }

    /**
     * Checks if the Git tool module is set to scan repositories at startup.
     *
     * @return bool True if set to scan at startup, false otherwise.
     */
    public function isScanStartup() {
        return $this->scanStartup == Config::ENABLED;
    }

    /**
     * Sets whether the Git tool module should scan repositories at startup.
     *
     * @param bool $scanStartup True to enable scanning at startup, false to disable.
     */
    public function setScanStartup($scanStartup) {
        $this->scanStartup = $scanStartup;
        Util::replaceInFile($this->bearsamppConf, array(
            '/^' . self::LOCAL_CFG_SCAN_STARTUP . '/' => self::LOCAL_CFG_SCAN_STARTUP . ' = "' . $this->scanStartup . '"'
        ));
    }
}
