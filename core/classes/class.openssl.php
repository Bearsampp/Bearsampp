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

    /**
     * Creates a certificate with the specified name and destination path.
     *
     * @param string $name The name of the certificate.
     * @param string|null $destPath The destination path where the certificate files will be saved. If null, the default SSL path is used.
     * @return bool True if the certificate was created successfully, false otherwise.
     */
    public function createCrt($name, $destPath = null)
    {
        $destPath = empty($destPath) ? Path::getSslPath() : $destPath;

        $subject = '"/C=US/O=Bearsampp/CN=' . $name . '"';
        $password = 'pass:bearsampp';
        $ppkPath = '"' . $destPath . '/' . $name . '.ppk"';
        $pubPath = '"' . $destPath . '/' . $name . '.pub"';
        $crtPath = '"' . $destPath . '/' . $name . '.crt"';
        $extension = 'SAN';
        $exe = '"' . Path::getOpenSslExe() . '"';

        // ext
        $extContent = PHP_EOL . '[' . $extension . ']' . PHP_EOL;
        $extContent .= 'subjectAltName=DNS:*.' . $name . ',DNS:' . $name . PHP_EOL;

        // tmp openssl.cfg
        $conf = Path::getTmpPath() . '/openssl_' . $name . '_' . UtilString::random() . '.cfg';
        file_put_contents($conf, file_get_contents(Path::getOpenSslConf()) . $extContent);

        // Properly quote the config path for batch commands
        $confPath = '"' . $conf . '"';

        // ppk - Updated for OpenSSL 3.x syntax
        $batch = $exe . ' genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -aes256 -pass ' . $password . ' -out ' . $ppkPath . ' -config ' . $confPath . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        // pub
        $batch .= $exe . ' rsa -in ' . $ppkPath . ' -passin ' . $password . ' -out ' . $pubPath . PHP_EOL . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        // crt
        $batch .= $exe . ' req -x509 -nodes -sha256 -new -key ' . $pubPath . ' -out ' . $crtPath . ' -passin ' . $password;
        $batch .= ' -subj ' . $subject . ' -reqexts ' . $extension . ' -extensions ' . $extension . ' -config ' . $confPath . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        $batch .= ':EOF' . PHP_EOL;
        $batch .= 'SET RESULT=KO' . PHP_EOL;
        $batch .= 'IF EXIST ' . $ppkPath . ' IF EXIST ' . $pubPath . ' IF EXIST ' . $crtPath . ' SET RESULT=OK' . PHP_EOL;
        $batch .= 'ECHO %RESULT%';

        Log::trace('Creating SSL Certificate for "' . $name . '"');
        $result = Batch::exec('createCertificate', $batch);

        if ($result === false) {
            Log::error('Batch execution failed for SSL Certificate generation of "' . $name . '"');
            return false;
        }

        if (!is_array($result)) {
            Log::error('Batch output capture failed for SSL Certificate generation of "' . $name . '". Result: ' . var_export($result, true));
            return false;
        }

        $success = isset($result[0]) && $result[0] == 'OK';
        if (!$success) {
            Log::error('SSL Certificate generation for "' . $name . '" failed. Batch output: ' . implode(' ', $result));
        }
        Log::trace('SSL Certificate generation for "' . $name . '": ' . ($success ? 'SUCCESS' : 'FAILURE'));

        return $success;
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
        global $bearsamppRoot;

        $ppkPath = Path::getSslPath() . '/' . $name . '.ppk';
        $pubPath = Path::getSslPath() . '/' . $name . '.pub';
        $crtPath = Path::getSslPath() . '/' . $name . '.crt';

        return is_file($ppkPath) && is_file($pubPath) && is_file($crtPath);
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
        $sslPath = Path::getSslPath();
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
        if (!is_file($crtPath)) {
            Log::trace('SSL certificate file missing: ' . $crtPath);
            return true;
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
        $destPath = empty($destPath) ? Path::getSslPath() : $destPath;

        // Basic validation for name to prevent arbitrary file deletion
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $name)) {
            Log::error('Invalid certificate name for removal: ' . $name);
            return false;
        }

        $ppkPath = $destPath . '/' . $name . '.ppk';
        $pubPath = $destPath . '/' . $name . '.pub';
        $crtPath = $destPath . '/' . $name . '.crt';

        Log::info('Removing SSL certificate: ' . $name . ' from ' . $destPath);
        return @unlink($ppkPath) && @unlink($pubPath) && @unlink($crtPath);
    }
}
