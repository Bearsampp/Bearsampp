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
    /**
     * Creates a certificate with the specified name and destination path.
     *
     * @param string $name The name of the certificate.
     * @param string|null $destPath The destination path where the certificate files will be saved. If null, the default SSL path is used.
     * @return bool True if the certificate was created successfully, false otherwise.
     */
    public function createCrt($name, $destPath = null)
    {
        global $bearsamppRoot, $bearsamppCore;
        $destPath = empty($destPath) ? $bearsamppRoot->getSslPath() : $destPath;

        $subject = '"/C=US/O=Bearsampp/CN=' . $name . '"';
        $password = 'pass:bearsampp';
        $ppkPath = '"' . $destPath . '/' . $name . '.ppk"';
        $pubPath = '"' . $destPath . '/' . $name . '.pub"';
        $crtPath = '"' . $destPath . '/' . $name . '.crt"';
        $extension = 'SAN';
        $exe = '"' . $bearsamppCore->getOpenSslExe() . '"';

        // ext
        $extContent = PHP_EOL . '[' . $extension . ']' . PHP_EOL;
        $extContent .= 'subjectAltName=DNS:*.' . $name . ',DNS:' . $name . PHP_EOL;

        // tmp openssl.cfg
        $conf = $bearsamppCore->getTmpPath() . '/openssl_' . $name . '_' . Util::random() . '.cfg';
        file_put_contents($conf, file_get_contents($bearsamppCore->getOpenSslConf()) . $extContent);

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
        $batch .= 'IF EXIST ' . $pubPath . ' IF EXIST ' . $crtPath . ' SET RESULT=OK' . PHP_EOL;
        $batch .= 'ECHO %RESULT%';

        Util::logTrace('Creating SSL Certificate for "' . $name . '"');
        $result = Batch::exec('createCertificate', $batch);

        $success = isset($result[0]) && $result[0] == 'OK';
        Util::logTrace('SSL Certificate generation for "' . $name . '": ' . ($success ? 'SUCCESS' : 'FAILURE'));

        return $success;
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

        $ppkPath = $bearsamppRoot->getSslPath() . '/' . $name . '.ppk';
        $pubPath = $bearsamppRoot->getSslPath() . '/' . $name . '.pub';
        $crtPath = $bearsamppRoot->getSslPath() . '/' . $name . '.crt';

        return is_file($ppkPath) && is_file($pubPath) && is_file($crtPath);
    }

    /**
     * Removes a certificate with the specified name.
     *
     * @param string $name The name of the certificate.
     * @return bool True if the certificate was removed successfully, false otherwise.
     */
    public function removeCrt($name)
    {
        global $bearsamppRoot;

        $ppkPath = $bearsamppRoot->getSslPath() . '/' . $name . '.ppk';
        $pubPath = $bearsamppRoot->getSslPath() . '/' . $name . '.pub';
        $crtPath = $bearsamppRoot->getSslPath() . '/' . $name . '.crt';

        return @unlink($ppkPath) && @unlink($pubPath) && @unlink($crtPath);
    }
}
