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

        $subject = '/C=US/ST=Oklahoma/L=McCord/O=bearsampp/CN=' . $name;
        $password = 'bearsampp';
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
        $confPath = '"' . $conf . '"';

        // Build the batch script
        $batch = '';

        // Generate private key (using modern parameters for OpenSSL 3.x)
        $batch .= $exe . ' genpkey -algorithm RSA -pkeyopt rsa_keygen_bits:2048 -out ' . $ppkPath . ' -pass pass:' . $password . ' -config ' . $confPath . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        // Extract public key
        $batch .= $exe . ' pkey -in ' . $ppkPath . ' -passin pass:' . $password . ' -out ' . $pubPath . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        // Generate self-signed certificate
        $batch .= $exe . ' req -x509 -sha256 -new -key ' . $pubPath . ' -out ' . $crtPath;
        $batch .= ' -subj "' . $subject . '" -reqexts ' . $extension . ' -extensions ' . $extension . ' -config ' . $confPath . ' -days 3650' . PHP_EOL;
        $batch .= 'IF %ERRORLEVEL% GEQ 1 GOTO EOF' . PHP_EOL . PHP_EOL;

        $batch .= ':EOF' . PHP_EOL;
        $batch .= 'SET RESULT=KO' . PHP_EOL;
        $batch .= 'IF EXIST ' . $ppkPath . ' IF EXIST ' . $pubPath . ' IF EXIST ' . $crtPath . ' SET RESULT=OK' . PHP_EOL;
        $batch .= 'DEL ' . $confPath . ' > NUL 2>&1' . PHP_EOL;
        $batch .= 'ECHO %RESULT%';

        $result = Batch::exec('createCertificate', $batch);
        return isset($result[0]) && $result[0] == 'OK';
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

    /**
     * Verifies a certificate to check if it's valid.
     *
     * @param string $name The name of the certificate.
     * @return bool True if the certificate is valid, false otherwise.
     */
    public function verifyCrt($name)
    {
        global $bearsamppRoot, $bearsamppCore;

        $crtPath = '"' . $bearsamppRoot->getSslPath() . '/' . $name . '.crt"';
        $exe = '"' . $bearsamppCore->getOpenSslExe() . '"';

        $batch = $exe . ' x509 -in ' . $crtPath . ' -noout -text';
        $result = Batch::exec('verifyCertificate', $batch);

        return is_array($result) && count($result) > 0;
    }
}
