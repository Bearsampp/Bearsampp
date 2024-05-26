<?php

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

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

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

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getRepos() {
        return $this->repos;
    }

    public function getExe() {
        return $this->exe;
    }

    public function getBash() {
        return $this->bash;
    }

    public function isScanStartup() {
        return $this->scanStartup == Config::ENABLED;
    }

    public function setScanStartup($scanStartup) {
        $this->scanStartup = $scanStartup;
        Util::replaceInFile($this->bearsamppConf, array(
            '/^' . self::LOCAL_CFG_SCAN_STARTUP . '/' => self::LOCAL_CFG_SCAN_STARTUP . ' = "' . $this->scanStartup . '"'
        ));
    }
}
