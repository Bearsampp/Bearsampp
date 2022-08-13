<?php

class BinSvn extends Module
{
    const SERVICE_NAME = 'bearsamppsvn';
    const SERVICE_PARAMS = '--service --root "%s" --listen-port "%d" --log-file "%s"';

    const ROOT_CFG_ENABLE = 'svnEnable';
    const ROOT_CFG_VERSION = 'svnVersion';

    const LOCAL_CFG_EXE = 'svnExe';
    const LOCAL_CFG_ADMIN = 'svnAdmin';
    const LOCAL_CFG_SERVE = 'svnServe';
    const LOCAL_CFG_PORT = 'svnPort';

    const CMD_VERSION = '--version';

    private $service;

    private $log;
    private $root;

    private $exe;
    private $adminExe;
    private $serveExe;
    private $port;

    public function __construct($id, $type) {
        Util::logInitClass($this);
        $this->reload($id, $type);
    }

    public function reload($id = null, $type = null) {
        global $bearsamppBs, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass($this);

        $this->name = $bearsamppLang->getValue(Lang::SVN);
        $this->version = $bearsamppConfig->getRaw(self::ROOT_CFG_VERSION);
        parent::reload($id, $type);

        $this->enable = $this->enable && $bearsamppConfig->getRaw(self::ROOT_CFG_ENABLE);
        $this->service = new Win32Service(self::SERVICE_NAME);
        $this->log = $bearsamppBs->getLogsPath() . '/svn.log';
        $this->root = $this->symlinkPath . '/repos';

        if ($this->bearsamppConfRaw !== false) {
            $this->exe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->adminExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_ADMIN];
            $this->serveExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_SERVE];
            $this->port = intval($this->bearsamppConfRaw[self::LOCAL_CFG_PORT]);
        }

        if (!$this->enable) {
            Util::logInfo($this->name . ' is not enabled!');
            return;
        }
        if (!is_dir($this->currentPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->currentPath));
            return;
        }
        if (!is_dir($this->symlinkPath)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $this->name . ' ' . $this->version, $this->symlinkPath));
            return;
        }
        if (!is_file($this->bearsamppConf)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), $this->name . ' ' . $this->version, $this->bearsamppConf));
            return;
        }
        if (!is_file($this->exe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->exe));
            return;
        }
        if (!is_file($this->adminExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->adminExe));
            return;
        }
        if (!is_file($this->serveExe)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_EXE_NOT_FOUND), $this->name . ' ' . $this->version, $this->serveExe));
            return;
        }
        if (empty($this->port)) {
            Util::logError(sprintf($bearsamppLang->getValue(Lang::ERROR_INVALID_PARAMETER), self::LOCAL_CFG_PORT, $this->port));
            return;
        }

        $this->service->setDisplayName(APP_TITLE . ' ' . $this->getName());
        $this->service->setBinPath($this->serveExe);
        $this->service->setParams(sprintf(self::SERVICE_PARAMS, $this->root, $this->port, $this->log));
        $this->service->setStartType(Win32Service::SERVICE_DEMAND_START);
        $this->service->setErrorControl(Win32Service::SERVER_ERROR_NORMAL);
    }

    protected function replaceAll($params) {
        $content = file_get_contents($this->bearsamppConf);

        foreach ($params as $key => $value) {
            $content = preg_replace('|' . $key . ' = .*|', $key . ' = ' . '"' . $value.'"', $content);
            $this->bearsamppConfRaw[$key] = $value;
            switch ($key) {
                case self::LOCAL_CFG_PORT:
                    $this->port = intval($value);
                    break;
            }
        }

        file_put_contents($this->bearsamppConf, $content);
    }

    public function changePort($port, $checkUsed = false, $wbProgressBar = null) {
        global $bearsamppWinbinder;

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        $port = intval($port);
        $bearsamppWinbinder->incrProgressBar($wbProgressBar);

        $isPortInUse = Util::isPortInUse($port);
        if (!$checkUsed || $isPortInUse === false) {
            // bearsampp.conf
            $this->setPort($port);
            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            // conf
            $this->update();
            $bearsamppWinbinder->incrProgressBar($wbProgressBar);

            return true;
        }

        Util::logDebug($this->getName() . ' port in used: ' . $port . ' - ' . $isPortInUse);
        return $isPortInUse;
    }

    public function checkPort($port, $showWindow = false) {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf($bearsamppLang->getValue(Lang::CHECK_PORT_TITLE), $this->getName(), $port);

        if (!Util::isValidPort($port)) {
            Util::logError($this->getName() . ' port not valid: ' . $port);
            return false;
        }

        $headers = Util::getFopenHttpHeaders('http://localhost:' . $port);
        if (!empty($headers)) {
            if (count($headers) == 1 && Util::startWith($headers[0], '( success (')) {
                Util::logDebug($this->getName() . ' port ' . $port . ' is used by: ' . $this->getName());
                if ($showWindow) {
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::PORT_USED_BY), $port, $this->getName()),
                        $boxTitle
                    );
                }
                return true;
            }
            Util::logDebug($this->getName() . ' port ' . $port . ' is used by another application');
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxWarning(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED_BY), $port),
                    $boxTitle
                );
            }
        } else {
            Util::logDebug($this->getName() . ' port ' . $port . ' is not used');
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::PORT_NOT_USED), $port),
                    $boxTitle
                );
            }
        }

        return false;
    }

    public function switchVersion($version, $showWindow = false) {
        Util::logDebug('Switch ' . $this->name . ' version to ' . $version);
        return $this->updateConfig($version, 0, $showWindow);
    }

    protected function updateConfig($version = null, $sub = 0, $showWindow = false) {
        global $bearsamppLang, $bearsamppApps, $bearsamppWinbinder;

        if (!$this->enable) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug(($sub > 0 ? str_repeat(' ', 2 * $sub) : '') . 'Update ' . $this->name . ' ' . $version . ' config...');

        $boxTitle = sprintf($bearsamppLang->getValue(Lang::SWITCH_VERSION_TITLE), $this->getName(), $version);

        $bearsamppConf = str_replace('svn' . $this->getVersion(), 'svn' . $version, $this->bearsamppConf);
        if (!file_exists($bearsamppConf)) {
            Util::logError('bearsampp config files not found for ' . $this->getName() . ' ' . $version);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::BEARSAMPP_CONF_NOT_FOUND_ERROR), $this->getName() . ' ' . $version),
                    $boxTitle
                );
            }
            return false;
        }

        $bearsamppConfRaw = parse_ini_file($bearsamppConf);
        if ($bearsamppConfRaw === false || !isset($bearsamppConfRaw[self::ROOT_CFG_VERSION]) || $bearsamppConfRaw[self::ROOT_CFG_VERSION] != $version) {
            Util::logError('bearsampp config file malformed for ' . $this->getName() . ' ' . $version);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::BEARSAMPP_CONF_MALFORMED_ERROR), $this->getName() . ' ' . $version),
                    $boxTitle
                );
            }
            return false;
        }

        // bearsampp.conf
        $this->setVersion($version);

        // websvn
       // $bearsamppApps->getWebsvn()->update($sub + 1);

        return true;
    }

    public function getCmdLineOutput($cmd) {
        $result = array(
            'syntaxOk' => false,
            'content'  => null,
        );

        $bin = $this->getExe();
        $removeLines = 0;
        $outputFrom = '';

        if (file_exists($this->getExe())) {
            $tmpResult = Batch::exec('svnGetCmdLineOutput', '"' . $bin . '" ' . $cmd . ' ' . $outputFrom);
            if ($tmpResult !== false && is_array($tmpResult)) {
                $result['syntaxOk'] = !Util::contains(trim($tmpResult[0]), 'invalid option');
                for ($i = 0; $i < $removeLines; $i++) {
                    unset($tmpResult[$i]);
                }
                $rebuildTmpResult = array();
                foreach ($tmpResult as $row) {
                    $rebuildTmpResult[] = Util::cp1252ToUtf8($row);
                }
                if ($cmd == self::CMD_VERSION) {
                    $result['content'] = trim($tmpResult[0]) . PHP_EOL . trim($tmpResult[1]);
                } else {
                    $result['content'] = trim(str_replace($bin, '', implode(PHP_EOL, $tmpResult)));
                }
            }
        }

        return $result;
    }

    public function findRepos() {
        $result = array();

        $handle = @opendir($this->root);
        if (!$handle) {
            return $result;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && is_dir($this->root . '/' . $file)) {
                $result[] = $this->root . '/' . $file;
            }
        }

        closedir($handle);
        ksort($result);
        return $result;
    }

    public function setVersion($version) {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace(self::ROOT_CFG_VERSION, $version);
        $this->reload();
    }

    public function getService() {
        return $this->service;
    }

    public function setEnable($enabled, $showWindow = false) {
        global $bearsamppConfig, $bearsamppLang, $bearsamppWinbinder;

        if ($enabled == Config::ENABLED && !is_dir($this->currentPath)) {
            Util::logDebug($this->getName() . ' cannot be enabled because bundle ' . $this->getVersion() . ' does not exist in ' . $this->currentPath);
            if ($showWindow) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::ENABLE_BUNDLE_NOT_EXIST), $this->getName(), $this->getVersion(), $this->currentPath),
                    sprintf($bearsamppLang->getValue(Lang::ENABLE_TITLE), $this->getName())
                );
            }
            $enabled = Config::DISABLED;
        }

        Util::logInfo($this->getName() . ' switched to ' . ($enabled == Config::ENABLED ? 'enabled' : 'disabled'));
        $this->enable = $enabled == Config::ENABLED;
        $bearsamppConfig->replace(self::ROOT_CFG_ENABLE, $enabled);

        $this->reload();
        if ($this->enable) {
            Util::installService($this, $this->port, null, $showWindow);
        } else {
            Util::removeService($this->service, $this->name);
        }
    }

    public function getLog() {
        return $this->log;
    }

    public function getRoot() {
        return $this->root;
    }

    public function getExe() {
        return $this->exe;
    }

    public function getAdminExe() {
        return $this->adminExe;
    }

    public function getServeExe() {
        return $this->serveExe;
    }

    public function getPort() {
        return $this->port;
    }

    public function setPort($port) {
        $this->replace(self::LOCAL_CFG_PORT, $port);
    }
}
