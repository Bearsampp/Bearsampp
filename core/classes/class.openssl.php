<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

class OpenSsl
{
    private $wbGenSslInputName;
    private $wbGenSslInputDest;
    private $wbGenSslBtnDest;
    private $wbGenSslProgressBar;
    private $wbGenSslBtnSave;
    private $wbGenSslBtnCancel;

    private $wbDelSslListCerts;
    private $wbDelSslInputDest;
    private $wbDelSslBtnDest;
    private $wbDelSslProgressBar;
    private $wbDelSslBtnDelete;
    private $wbDelSslBtnCancel;
    private $rootCaName = 'BearsamppRootCA';


    /**
     * Ensures that the mkcert executable exists, attempting to download it if missing.
     *
     * @return bool True if mkcert exists or was successfully downloaded.
     */
    private function ensureMkcertExeExists()
    {
        $mkcertExe = Path::getMkcertExe();
        if (file_exists($mkcertExe)) {
            return true;
        }

        Log::error('mkcert.exe missing at: ' . $mkcertExe . '. It must be fetched during build time (prepareBase/buildFull/buildLite).');
        return false;
    }

    /**
     * Ensures that the SSL directory exists, creating it if necessary.
     *
     * @return string The SSL path.
     */
    private function ensureSslDirExists()
    {
        $sslPath = Path::getSslPath();
        if (!is_dir($sslPath)) {
            Log::info('SSL directory missing, creating: ' . $sslPath);
            if (mkdir($sslPath, 0700, true)) {
                @chmod($sslPath, 0700);
                // Create .gitignore if the directory was just created
                $gitignorePath = $sslPath . '/.gitignore';
                if (!file_exists($gitignorePath)) {
                    file_put_contents($gitignorePath, '# git holder' . PHP_EOL);
                }
            } else {
                Log::error('Failed to create SSL directory: ' . $sslPath);
            }
        } else {
            // Even if directory exists, ensure .gitignore is present
            $gitignorePath = $sslPath . '/.gitignore';
            if (!file_exists($gitignorePath)) {
                file_put_contents($gitignorePath, '# git holder' . PHP_EOL);
            }
        }

        if (!is_readable($sslPath) || !is_writable($sslPath)) {
            Log::warning('SSL directory is not fully accessible. Attempting to relax permissions: ' . $sslPath);

            // Set permissive permissions for local dev environment (0755 allows owner RWX, group/others RX)
            @chmod($sslPath, 0755);
            clearstatcache(true, $sslPath);

            if (!is_readable($sslPath) || !is_writable($sslPath)) {
                Log::error('SSL directory is still not readable/writable after permission adjustment: ' . $sslPath);
            }
        }

        return $sslPath;
    }

    /**
     * Creates a new Root CA and reinstalls it, then rebuilds all certificates.
     *
     * @return bool True if successful.
     */
    public function makeRootCa()
    {
        if (!$this->ensureMkcertExeExists()) {
            return false;
        }
        $destPath = Path::getSslPath();
        $mkcertExe = Path::getMkcertExe();

        Log::info('Creating new Root CA and installing it...');
        
        $rootCaPath = Path::getSslPath() . '/' . Path::getMkcertRootCaName();
        $batch = 'SET "CAROOT=' . Path::formatWindowsPath(Path::getSslPath()) . '"' . PHP_EOL;
        $batch .= '"' . $mkcertExe . '" -uninstall' . PHP_EOL;
        $batch .= '"' . $mkcertExe . '" -install' . PHP_EOL;
        $batch .= 'IF EXIST "' . Path::formatWindowsPath($rootCaPath) . '" (ECHO OK)' . PHP_EOL;
        
        // Wait for the Root CA file to appear or timeout
        $result = Batch::exec('mkcertMakeRootCa', $batch);
        
        if (!file_exists($rootCaPath)) {
            Log::error('Failed to create Root CA file at: ' . $rootCaPath);
            return false;
        }

        // Display info about the new Root CA
        $batch = 'SET "CAROOT=' . Path::formatWindowsPath(Path::getSslPath()) . '"' . PHP_EOL;
        $batch .= '"' . $mkcertExe . '" -CAROOT' . PHP_EOL;
        $caRootInfo = Batch::exec('mkcertCaRootInfo', $batch);
        if ($caRootInfo && isset($caRootInfo[0])) {
            Log::info('mkcert CAROOT is set to: ' . $caRootInfo[0]);
        }

        Log::info('Root CA created. Rebuilding all existing certificates and ensuring localhost exists...');
        
        // Ensure localhost is created/rebuilt
        $this->createCrt('localhost');
        
        return $this->rebuildAllCerts();
    }

    /**
     * Rebuilds all existing certificates in the SSL directory.
     *
     * @return bool True if all certificates were rebuilt successfully.
     */
    public function rebuildAllCerts()
    {
        $certs = $this->getCrts();
        $success = true;

        foreach ($certs as $cert) {
            Log::info('Rebuilding certificate: ' . $cert);
            if (!$this->createCrt($cert)) {
                Log::error('Failed to rebuild certificate: ' . $cert);
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Validates the certificate name to prevent command injection attacks.
     * Requires names to start with alphanumeric character to prevent mkcert flag injection.
     * Uses filesystem-safe whitelist (same as removeCrt) to support existing cert names
     * and prevent CMD metacharacter injection. Allows: alphanumeric, dots, dashes, underscores.
     *
     * @param string $name The certificate name to validate.
     * @return bool True if the name is valid, false otherwise.
     */
    private function validateCertificateName($name)
    {
        if (empty($name)) {
            return false;
        }

        // Filesystem-safe whitelist with mandatory alphanumeric first character:
        // - Prevents CLI flag injection (leading `-`)
        // - Prevents relative path traversal (leading `.`)
        // - Allows remaining chars to be alphanumeric, dots, dashes, underscores
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/', $name)) {
            return false;
        }

        return true;
    }

    /**
     * Creates a certificate with the specified name and destination path.
     *
     * @param string $name The name of the certificate.
     * @param string|null $destPath The destination path where the certificate files will be saved. If null, the default SSL path is used.
     * @return bool True if the certificate was created successfully, false otherwise.
     */
    public function createCrt($name, $destPath = null)
    {
        Log::trace('createCrt called for: ' . $name . ($destPath ? ' (dest: ' . $destPath . ')' : ''));

        if (!$this->validateCertificateName($name)) {
            Log::error('Invalid certificate name: ' . $name);
            return false;
        }

        if (!$this->ensureMkcertExeExists()) {
            return false;
        }
        if (empty($destPath)) {
            $destPath = $this->ensureSslDirExists();
        }
        $mkcertExe = Path::getMkcertExe();

        if (!$this->ensureRootCaExists($destPath)) {
            Log::error('Failed to ensure Root CA exists for: ' . $name);
            return false;
        }

        $crtPath = '"' . Path::formatWindowsPath($destPath . '/' . $name . '.crt') . '"';
        $pubPath = '"' . Path::formatWindowsPath($destPath . '/' . $name . '.pub') . '"';
        $keyPath = '"' . Path::formatWindowsPath($destPath . '/' . $name . '.ppk') . '"'; // Using .ppk as requested in previous tasks
        $opensslExe = '"' . Path::formatWindowsPath(Path::getOpenSslExe()) . '"';

        $batch = 'SET "CAROOT=' . Path::formatWindowsPath($destPath) . '"' . PHP_EOL;

        $mkcertNames = '"' . $name . '"';
        if ($name === 'localhost') {
            $mkcertNames .= ' 127.0.0.1 ::1';
        } else {
            $mkcertNames .= ' "*.' . $name . '" localhost 127.0.0.1 ::1';
        }

        Log::trace('Executing mkcert for "' . $name . '"');
        // Use -- to terminate flag parsing before hostname arguments as defense-in-depth
        $batch .= '"' . $mkcertExe . '" -cert-file ' . $crtPath . ' -key-file ' . $keyPath . ' -- ' . $mkcertNames . PHP_EOL;
        $batch .= $opensslExe . " rsa -in " . $keyPath . " -out " . $keyPath . " -passin pass:" . PHP_EOL;
        $batch .= "COPY /Y " . $keyPath . " " . $pubPath . PHP_EOL;
        $batch .= 'IF EXIST ' . $crtPath . ' IF EXIST ' . $keyPath . ' ECHO OK' . PHP_EOL;

        Log::trace('Creating SSL Certificate for "' . $name . '" using mkcert. Batch content: ' . $batch);
        $result = Batch::exec('createCertificateMkcert', $batch);

        if ($result === false || !is_array($result)) {
            Log::error('Batch execution failed for mkcert generation of "' . $name . '". Check logs for createCertificateMkcert.');
            return false;
        }

        $success = false;
        foreach ($result as $line) {
            if (trim($line) === 'OK') {
                $success = true;
                break;
            }
        }
        
        if (!$success) {
            Log::error('mkcert generation for "' . $name . '" did not return OK. Output: ' . implode(' | ', $result));
        }
        Log::trace('mkcert generation for "' . $name . '": ' . ($success ? 'SUCCESS' : 'FAILURE'));

        return $success;
    }

    /**
     * Ensures that the Root CA exists, creating it if necessary.
     *
     * @param string $destPath The destination path.
     * @return bool True if the Root CA exists or was created successfully.
     */
    private function ensureRootCaExists($destPath)
    {
        if (!$this->ensureMkcertExeExists()) {
            return false;
        }
        $mkcertExe = Path::getMkcertExe();

        $rootCaPath = $destPath . '/' . Path::getMkcertRootCaName(); // mkcert default root CA name
        if (!file_exists($rootCaPath)) {
            Log::info('Root CA missing at ' . $rootCaPath . '. Running mkcert -install');
            $batch = 'SET "CAROOT=' . Path::formatWindowsPath($destPath) . '"' . PHP_EOL;
            $batch .= '"' . $mkcertExe . '" -install' . PHP_EOL;
            $batch .= 'IF EXIST "' . Path::formatWindowsPath($rootCaPath) . '" (ECHO OK)' . PHP_EOL;
            $result = Batch::exec('mkcertInstall', $batch);
            
            if ($result === false) {
                Log::error('Batch execution failed for mkcert -install');
                return false;
            }

            // Re-check after installation
            if (!file_exists($rootCaPath)) {
                Log::error('Root CA still missing after mkcert -install at: ' . $rootCaPath);
                return false;
            }
            Log::info('Root CA successfully created and verified.');
        }
        return true;
    }

    /**
     * Displays a WinBinder GUI for deleting an SSL certificate.
     */
    public function delSslCertificate()
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $initServerName = 'test.local';
        $initDocumentRoot = Path::formatWindowsPath(Path::getSslPath());

        $bearsamppWinbinder->reset();
        $wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::DELSSL_TITLE), 490, 160, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $wbLabelName = $bearsamppWinbinder->createLabel($wbWindow, $bearsamppLang->getValue(Lang::NAME) . ' :', 15, 15, 85, null, WBC_RIGHT);
        $this->wbDelSslListCerts = $bearsamppWinbinder->createInputText($wbWindow, $initServerName, 105, 13, 150, null);

        $wbLabelDest = $bearsamppWinbinder->createLabel($wbWindow, $bearsamppLang->getValue(Lang::TARGET) . ' :', 15, 45, 85, null, WBC_RIGHT);
        $this->wbDelSslInputDest = $bearsamppWinbinder->createInputText($wbWindow, $initDocumentRoot, 105, 43, 190, null, null, WBC_READONLY);
        $this->wbDelSslBtnDest = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110);

        $this->wbDelSslProgressBar = $bearsamppWinbinder->createProgressBar($wbWindow, 3, 15, 97, 275);
        $this->wbDelSslBtnDelete = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_DELETE), 300, 92);
        $this->wbDelSslBtnCancel = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 92);

        $bearsamppWinbinder->setHandler($wbWindow, $this, 'delSslCertificateHandler');

        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Handler for the SSL certificate deletion WinBinder GUI.
     */
    public function delSslCertificateHandler($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppOpenSsl, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbDelSslBtnDest[WinBinder::CTRL_ID]:
                $target = $bearsamppWinbinder->getText($this->wbDelSslInputDest[WinBinder::CTRL_OBJ]);
                $target = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::GENSSL_PATH), $target);
                if ($target && is_dir($target)) {
                    $bearsamppWinbinder->setText($this->wbDelSslInputDest[WinBinder::CTRL_OBJ], $target . '\\');
                }
                break;
            case $this->wbDelSslBtnDelete[WinBinder::CTRL_ID]:
                $cert = $bearsamppWinbinder->getText($this->wbDelSslListCerts[WinBinder::CTRL_OBJ]);
                $target = $bearsamppWinbinder->getText($this->wbDelSslInputDest[WinBinder::CTRL_OBJ]);

                if ($cert) {
                    $existingCerts = $this->getCrts();
                    if (!in_array($cert, $existingCerts)) {
                        $bearsamppWinbinder->messageBoxError(sprintf($bearsamppLang->getValue(Lang::ERROR_FILE_NOT_FOUND), $cert, $target), $bearsamppLang->getValue(Lang::DELSSL_TITLE));
                        return;
                    }

                    $bearsamppWinbinder->setProgressBarMax($this->wbDelSslProgressBar, 3);
                    $bearsamppWinbinder->incrProgressBar($this->wbDelSslProgressBar);

                    $target = Path::formatUnixPath($target);
                    if ($bearsamppOpenSsl->removeCrt($cert, $target)) {
                        $bearsamppWinbinder->incrProgressBar($this->wbDelSslProgressBar);
                        $bearsamppWinbinder->messageBoxInfo(
                            sprintf($bearsamppLang->getValue(Lang::DELSSL_DELETED), $cert),
                            $bearsamppLang->getValue(Lang::DELSSL_TITLE)
                        );
                        $bearsamppWinbinder->destroyWindow($window);
                    } else {
                        $bearsamppWinbinder->messageBoxError($bearsamppLang->getValue(Lang::DELSSL_DELETED_ERROR), $bearsamppLang->getValue(Lang::DELSSL_TITLE));
                        $bearsamppWinbinder->resetProgressBar($this->wbDelSslProgressBar);
                    }
                }
                break;
            case IDCLOSE:
            case $this->wbDelSslBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }

    /**
     * Checks if a certificate with the specified name exists.
     *
     * @param string $name The name of the certificate.
     * @return bool True if the certificate exists, false otherwise.
     */
    public function existsCrt($name)
    {
        $ppkPath = Path::getSslPath() . '/' . $name . '.ppk';
        $crtPath = Path::getSslPath() . '/' . $name . '.crt';

        return is_file($ppkPath) && is_file($crtPath);
    }

    /**
     * Displays a WinBinder GUI for generating an SSL certificate.
     */
    public function genSslCertificate()
    {
        global $bearsamppLang, $bearsamppWinbinder;

        $initServerName = 'test.local';
        $initDocumentRoot = Path::formatWindowsPath(Path::getSslPath());

        $bearsamppWinbinder->reset();
        $wbWindow = $bearsamppWinbinder->createAppWindow($bearsamppLang->getValue(Lang::GENSSL_TITLE), 490, 160, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP);

        $wbLabelName = $bearsamppWinbinder->createLabel($wbWindow, $bearsamppLang->getValue(Lang::NAME) . ' :', 15, 15, 85, null, WBC_RIGHT);
        $this->wbGenSslInputName = $bearsamppWinbinder->createInputText($wbWindow, $initServerName, 105, 13, 150, null);

        $wbLabelDest = $bearsamppWinbinder->createLabel($wbWindow, $bearsamppLang->getValue(Lang::TARGET) . ' :', 15, 45, 85, null, WBC_RIGHT);
        $this->wbGenSslInputDest = $bearsamppWinbinder->createInputText($wbWindow, $initDocumentRoot, 105, 43, 190, null, null, WBC_READONLY);
        $this->wbGenSslBtnDest = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_BROWSE), 300, 43, 110);

        $this->wbGenSslProgressBar = $bearsamppWinbinder->createProgressBar($wbWindow, 3, 15, 97, 275);
        $this->wbGenSslBtnSave = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_SAVE), 300, 92);
        $this->wbGenSslBtnCancel = $bearsamppWinbinder->createButton($wbWindow, $bearsamppLang->getValue(Lang::BUTTON_CANCEL), 387, 92);

        $bearsamppWinbinder->setHandler($wbWindow, $this, 'genSslCertificateHandler');

        $bearsamppWinbinder->mainLoop();
        $bearsamppWinbinder->reset();
    }

    /**
     * Handler for the SSL certificate generation WinBinder GUI.
     */
    public function genSslCertificateHandler($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppOpenSsl, $bearsamppWinbinder;

        switch ($id) {
            case $this->wbGenSslBtnDest[WinBinder::CTRL_ID]:
                $target = $bearsamppWinbinder->getText($this->wbGenSslInputDest[WinBinder::CTRL_OBJ]);
                $target = $bearsamppWinbinder->sysDlgPath($window, $bearsamppLang->getValue(Lang::GENSSL_PATH), $target);
                if ($target && is_dir($target)) {
                    $bearsamppWinbinder->setText($this->wbGenSslInputDest[WinBinder::CTRL_OBJ], $target . '\\');
                }
                break;
            case $this->wbGenSslBtnSave[WinBinder::CTRL_ID]:
                $name = $bearsamppWinbinder->getText($this->wbGenSslInputName[WinBinder::CTRL_OBJ]);
                $target = $bearsamppWinbinder->getText($this->wbGenSslInputDest[WinBinder::CTRL_OBJ]);

                $bearsamppWinbinder->setProgressBarMax($this->wbGenSslProgressBar, 3);
                $bearsamppWinbinder->incrProgressBar($this->wbGenSslProgressBar);

                $target = Path::formatUnixPath($target);
                if ($bearsamppOpenSsl->createCrt($name, $target)) {
                    $bearsamppWinbinder->incrProgressBar($this->wbGenSslProgressBar);
                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf($bearsamppLang->getValue(Lang::GENSSL_CREATED), $name),
                        $bearsamppLang->getValue(Lang::GENSSL_TITLE));
                    $bearsamppWinbinder->destroyWindow($window);
                } else {
                    $bearsamppWinbinder->messageBoxError($bearsamppLang->getValue(Lang::GENSSL_CREATED_ERROR), $bearsamppLang->getValue(Lang::GENSSL_TITLE));
                    $bearsamppWinbinder->resetProgressBar($this->wbGenSslProgressBar);
                }
                break;
            case IDCLOSE:
            case $this->wbGenSslBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow($window);
                break;
        }
    }

    /**
     * Retrieves existing certificates from the SSL directory.
     *
     * @return array List of certificate names.
     */
    public function getCrts()
    {
        $sslPath = $this->ensureSslDirExists();
        $certs = [];
        if (is_dir($sslPath)) {
            $files = glob($sslPath . '/*.crt');
            if ($files !== false) {
                foreach ($files as $file) {
                    $certs[] = basename($file, '.crt');
                }
            }
        }
        sort($certs);
        return $certs;
    }

    /**
     * Checks if a certificate with the specified name is expired or about to expire.
     *
     * @param string $name The name of the certificate.
     * @return bool True if the certificate is expired or missing, false otherwise.
     */
    public function isExpired($name)
    {
        $crtPath = Path::getSslPath() . '/' . $name . '.crt';
        $pubPath = Path::getSslPath() . '/' . $name . '.pub';

        if (!is_file($crtPath)) {
            Log::trace('SSL certificate file missing: ' . $crtPath);
            return true;
        }

        if (!is_file($pubPath)) {
            Log::trace('SSL public certificate file missing: ' . $pubPath);
            return true;
        }

        if (!extension_loaded('openssl')) {
            Log::warning('OpenSSL extension not loaded. Cannot parse certificate for expiry check. Assuming NOT expired if file exists.');
            return false;
        }

        $crtContent = file_get_contents($crtPath);
        if ($crtContent === false) {
            Log::error('Could not read certificate file: ' . $crtPath);
            return true;
        }

        $certInfo = openssl_x509_parse($crtContent);
        if ($certInfo === false) {
            Log::error('Could not parse certificate: ' . $crtPath . '. OpenSSL error: ' . openssl_error_string());
            return true;
        }

        if (isset($certInfo['validTo_time_t'])) {
            $isExpired = $certInfo['validTo_time_t'] < time();
            if ($isExpired) {
                Log::trace('SSL certificate expired: ' . $name . ' (Expired on ' . date('Y-m-d H:i:s', $certInfo['validTo_time_t']) . ')');
            }
            return $isExpired;
        }

        Log::error('Could not find expiry date in certificate: ' . $crtPath);
        return true;
    }

    /**
     * Removes a certificate with the specified name.
     *
     * @param string $name The name of the certificate.
     * @param string|null $destPath The destination path where the certificate files are saved. If null, the default SSL path is used.
     * @return bool True if the certificate was removed successfully, false otherwise.
     */
    public function removeCrt($name, $destPath = null)
    {
        if ($name === 'localhost') {
            Log::warning('Attempted to remove protected "localhost" certificate. Operation cancelled.');
            return false;
        }
        $destPath = empty($destPath) ? $this->ensureSslDirExists() : $destPath;

        // Basic validation for name to prevent arbitrary file deletion
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $name)) {
            Log::error('Invalid certificate name for removal: ' . $name);
            return false;
        }

        $ppkPath = $destPath . '/' . $name . '.ppk';
        $crtPath = $destPath . '/' . $name . '.crt';
        $pubPath = $destPath . '/' . $name . '.pub';

        Log::info('Removing SSL certificate: ' . $name . ' from ' . $destPath);
        return @unlink($ppkPath) && @unlink($crtPath) && @unlink($pubPath);
    }
}
