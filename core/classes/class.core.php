<?php
/*
 *
 *  * Copyright (c) 2021-2024 Bearsampp
 *  * License:  GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class Core
 *
 * This class provides core functionalities and constants for the Bearsampp application.
 * It includes methods for retrieving paths, managing application versions, and handling
 * various executable files and configurations.
 */
class Core
{
    // Constants for various file names and versions
    const isRoot_FILE = 'root.php';
    const PATH_WIN_PLACEHOLDER = '~BEARSAMPP_WIN_PATH~';
    const PATH_LIN_PLACEHOLDER = '~BEARSAMPP_LIN_PATH~';

    const PHP_VERSION = '5.4.23';
    const PHP_EXE = 'php-win.exe';
    const PHP_CONF = 'php.ini';

    const SETENV_VERSION = '1.09';
    const SETENV_EXE = 'SetEnv.exe';

    const NSSM_VERSION = '2.24';
    const NSSM_EXE = 'nssm.exe';

    const OPENSSL_VERSION = '1.1.0c';
    const OPENSSL_EXE = 'openssl.exe';
    const OPENSSL_CONF = 'openssl.cfg';

    const HOSTSEDITOR_VERSION = '1.3';
    const HOSTSEDITOR_EXE = 'hEdit_x64.exe';

    const LN_VERSION = '2.928';
    const LN_EXE = 'ln.exe';

    const PWGEN_VERSION = '3.5.4';
    const PWGEN_EXE = "PWGenPortable.exe";

    const APP_VERSION = 'version.dat';
    const LAST_PATH = 'lastPath.dat';
    const EXEC = 'exec.dat';
    const LOADING_PID = 'loading.pid';

    const SCRIPT_EXEC_SILENT = 'execSilent.vbs';

    /**
     * Core constructor.
     *
     * Loads the WinBinder extension if available.
     */
    public function __construct()
    {
        if ( extension_loaded( 'winbinder' ) ) {
            require_once $this->getLibsPath() . '/winbinder/winbinder.php';
        }
    }

    /**
     * Retrieves the path to the language files.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the language files.
     */
    public function getLangsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/langs';
    }

    /**
     * Retrieves the path to the libraries.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the libraries.
     */
    public function getLibsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/libs';
    }

    /**
     * Retrieves the path to the resources.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the resources.
     */
    public function getResourcesPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/resources';
    }

    /**
     * Retrieves the path to the icons.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the icons.
     */
    public function getIconsPath($aetrayPath = false)
    {
        global $bearsamppCore;

        return $bearsamppCore->getResourcesPath( $aetrayPath ) . '/homepage/img/icons';
    }

    /**
     * Retrieves the path to the images.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the icons.
     */
    public function getImagesPath($aetrayPath = false)
    {
        global $bearsamppCore;

        return $bearsamppCore->getResourcesPath( $aetrayPath ) . '/homepage/img';
    }

    /**
     * Retrieves the path to the scripts.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the scripts.
     */
    public function getScriptsPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/scripts';
    }

    public function getHomepagePath($aetrayPath = false)
    {
        return $this->getResourcesPath( $aetrayPath ) . '/homepage';
    }

    public function getAjaxPath($aetrayPath = false)
    {
        return $this->getHomepagePath( $aetrayPath ) . '/ajax';
    }

    /**
     * Retrieves the path to a specific script.
     *
     * @param   string  $type  The type of script.
     *
     * @return string The path to the script.
     */
    public function getScript($type)
    {
        return $this->getScriptsPath() . '/' . $type;
    }

    /**
     * Retrieves the path to the temporary directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the temporary directory.
     */
    public function getTmpPath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/tmp';
    }

    /**
     * Retrieves the path to the root file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the root file.
     */
    public function getisRootFilePath($aetrayPath = false)
    {
        global $bearsamppRoot;

        return $bearsamppRoot->getCorePath( $aetrayPath ) . '/' . self::isRoot_FILE;
    }

    /**
     * Retrieves the application version.
     *
     * @return string|null The application version or null if not found.
     */
    public function getAppVersion()
    {
        global $bearsamppLang;

        $filePath = $this->getResourcesPath() . '/' . self::APP_VERSION;
        if ( !is_file( $filePath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), APP_TITLE, $filePath ) );

            return null;
        }

        return trim( file_get_contents( $filePath ) );
    }

    /**
     * Retrieves the path to the last path file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the last path file.
     */
    public function getLastPath($aetrayPath = false)
    {
        return $this->getResourcesPath( $aetrayPath ) . '/' . self::LAST_PATH;
    }

    /**
     * Retrieves the content of the last path file.
     *
     * @return string|false The content of the last path file or false on failure.
     */
    public function getLastPathContent()
    {
        return @file_get_contents( $this->getLastPath() );
    }

    /**
     * Retrieves the path to the exec file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the exec file.
     */
    public function getExec($aetrayPath = false)
    {
        return $this->getTmpPath( $aetrayPath ) . '/' . self::EXEC;
    }

    /**
     * Sets the content of the exec file.
     *
     * @param   string  $action  The content to set in the exec file.
     */
    public function setExec($action)
    {
        file_put_contents( $this->getExec(), $action );
    }

    /**
     * Retrieves the path to the loading PID file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the loading PID file.
     */
    public function getLoadingPid($aetrayPath = false)
    {
        return $this->getResourcesPath( $aetrayPath ) . '/' . self::LOADING_PID;
    }

    /**
     * Adds a PID to the loading PID file.
     *
     * @param   int  $pid  The PID to add.
     */
    public function addLoadingPid($pid)
    {
        file_put_contents( $this->getLoadingPid(), $pid . PHP_EOL, FILE_APPEND );
    }

    /**
     * Retrieves the path to the PHP directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PHP directory.
     */
    public function getPhpPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/php';
    }

    /**
     * Retrieves the path to the PHP executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PHP executable.
     */
    public function getPhpExe($aetrayPath = false)
    {
        return $this->getPhpPath( $aetrayPath ) . '/' . self::PHP_EXE;
    }

    /**
     * Retrieves the path to the SetEnv directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the SetEnv directory.
     */
    public function getSetEnvPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/setenv';
    }

    /**
     * Retrieves the path to the SetEnv executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the SetEnv executable.
     */
    public function getSetEnvExe($aetrayPath = false)
    {
        return $this->getSetEnvPath( $aetrayPath ) . '/' . self::SETENV_EXE;
    }

    /**
     * Retrieves the path to the NSSM directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the NSSM directory.
     */
    public function getNssmPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/nssm';
    }

    /**
     * Retrieves the path to the NSSM executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the NSSM executable.
     */
    public function getNssmExe($aetrayPath = false)
    {
        return $this->getNssmPath( $aetrayPath ) . '/' . self::NSSM_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL directory.
     */
    public function getOpenSslPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/openssl';
    }

    /**
     * Retrieves the path to the OpenSSL executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL executable.
     */
    public function getOpenSslExe($aetrayPath = false)
    {
        return $this->getOpenSslPath( $aetrayPath ) . '/' . self::OPENSSL_EXE;
    }

    /**
     * Retrieves the path to the OpenSSL configuration file.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the OpenSSL configuration file.
     */
    public function getOpenSslConf($aetrayPath = false)
    {
        return $this->getOpenSslPath( $aetrayPath ) . '/' . self::OPENSSL_CONF;
    }

    /**
     * Retrieves the path to the HostsEditor directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the HostsEditor directory.
     */
    public function getHostsEditorPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/hostseditor';
    }

    /**
     * Retrieves the path to the HostsEditor executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the HostsEditor executable.
     */
    public function getHostsEditorExe($aetrayPath = false)
    {
        return $this->getHostsEditorPath( $aetrayPath ) . '/' . self::HOSTSEDITOR_EXE;
    }

    /**
     * Retrieves the path to the LN directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the LN directory.
     */
    public function getLnPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/ln';
    }

    /**
     * Retrieves the path to the LN executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the LN executable.
     */
    public function getLnExe($aetrayPath = false)
    {
        return $this->getLnPath( $aetrayPath ) . '/' . self::LN_EXE;
    }

    /**
     * Retrieves the path to the PWGen directory.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PWGen directory.
     */
    public function getPwgenPath($aetrayPath = false)
    {
        return $this->getLibsPath( $aetrayPath ) . '/pwgen';
    }

    /**
     * Retrieves the path to the PWGen executable.
     *
     * @param   bool  $aetrayPath  Whether to format the path for AeTrayMenu.
     *
     * @return string The path to the PWGen executable.
     */
    public function getPwgenExe($aetrayPath = false)
    {
        return $this->getPwgenPath( $aetrayPath ) . '/' . self::PWGEN_EXE;
    }

    /**
     * Provides a string representation of the core object.
     *
     * @return string A string describing the core object.
     */
    public function __toString()
    {
        return 'core object';
    }

/**
     * Unzips a file to the specified directory and provides progress updates.
     *
     * This method uses the 7-Zip command-line tool to extract the contents of a zip file.
     * It first tests the archive to determine the number of files to be extracted, then
     * proceeds with the extraction while providing progress updates via a callback function.
     *
     * @param   string         $filePath          The path to the zip file.
     * @param   string         $destination       The directory to extract the files to.
     * @param   callable|null  $progressCallback  A callback function to report progress. The callback receives two parameters:
     *                                            - int $currentFile: The current file number being extracted.
     *                                            - int $totalFiles: The total number of files to be extracted.
     *
     * @global  object         $bearsamppRoot     Global object to get core paths.
     *
     * @return array|false An array containing the result of the extraction on success or failure:
     *                     - On success: ['success' => true, 'numFiles' => int]
     *                     - On failure: ['error' => string, 'numFiles' => int]
     *                     - Returns false if the 7-Zip executable is not found.
     */
    public function unzipFile($filePath, $destination, $progressCallback = null)
    {
        global $bearsamppRoot;

        $sevenZipPath = $this->getLibsPath() . '/7zip/7za.exe';

        if ( !file_exists( $sevenZipPath ) ) {
            Util::logError( '7za.exe not found at: ' . $sevenZipPath );

            return false;
        }

        // Command to test the archive and get the number of files
        $testCommand = escapeshellarg( $sevenZipPath ) . ' t ' . escapeshellarg( $filePath ) . ' -y -bsp1';
        $testOutput  = shell_exec( $testCommand );

        // Extract the number of files from the test command output
        preg_match( '/Files: (\d+)/', $testOutput, $matches );
        $numFiles = isset( $matches[1] ) ? (int) $matches[1] : 0;
        Util::logTrace( 'Number of files to be extracted: ' . $numFiles );

        // Command to extract the archive
        $command = escapeshellarg( $sevenZipPath ) . ' x ' . escapeshellarg( $filePath ) . ' -y -bsp1 -bb0 -o' . escapeshellarg( $destination );
        Util::logTrace( 'Executing command: ' . $command );

        $process = popen( $command, 'rb' );

        if ( $process ) {
            $buffer = '';
            while ( !feof( $process ) ) {
                $buffer .= fread( $process, 8192 ); // Read in chunks of 8KB
                while ( ($pos = strpos( $buffer, "\r" )) !== false ) {
                    $line   = substr( $buffer, 0, $pos );
                    $buffer = substr( $buffer, $pos + 1 );
                    $line   = trim( $line ); // Remove any leading/trailing whitespace
                    Util::logTrace( "Processing line: $line" );

                    // Check if the line indicates everything is okay
                    if ( $line === "Everything is Ok" ) {
                        if ( $progressCallback ) {
                            Util::logTrace( "Extraction progress: 100%" );
                            call_user_func( $progressCallback, 100 );
                            Util::logTrace( "Progress callback called with percentage: 100" );
                        }
                    }
                    else if ( $progressCallback && preg_match( '/(?:^|\s)(\d+)%/', $line, $matches ) ) {
                        $currentPercentage = intval( $matches[1] );
                        Util::logTrace( "Extraction progress: $currentPercentage%" );
                        call_user_func( $progressCallback, $currentPercentage );
                        Util::logTrace( "Progress callback called with percentage: $currentPercentage" );
                    }
                    else {
                        Util::logTrace( "Line did not match pattern: $line" );
                    }
                }
            }

            // Process any remaining data in the buffer
            if ( !empty( $buffer ) ) {
                $line = trim( $buffer );
                Util::logTrace( "Processing remaining line: $line" );

                // Check if the remaining line indicates everything is okay
                if ( $line === "Everything is Ok" ) {
                    if ( $progressCallback ) {
                        Util::logTrace( "Extraction progress: 100%" );
                        call_user_func( $progressCallback, 100 );
                        Util::logTrace( "Progress callback called with percentage: 100" );
                    }
                }
                else if ( $progressCallback && preg_match( '/(?:^|\s)(\d+)%/', $line, $matches ) ) {
                    $currentPercentage = intval( $matches[1] );
                    Util::logTrace( "Extraction progress: $currentPercentage%" );
                    call_user_func( $progressCallback, $currentPercentage );
                    Util::logTrace( "Progress callback called with percentage: $currentPercentage" );
                }
                else {
                    Util::logTrace( "Remaining line did not match pattern: $line" );
                }
            }

            $returnVar = pclose( $process );
            Util::logTrace( 'Command return value: ' . $returnVar );

            // Set progress to 100% if the command was successful
            if ( $returnVar === 0 && $progressCallback ) {
                Util::logTrace( "Extraction completed successfully. Setting progress to 100%" );
                call_user_func( $progressCallback, 100 );
                Util::logTrace( "Progress callback called with percentage: 100" );

                // Adding a small delay to ensure the progress bar update is processed
                usleep( 100000 ); // 100 milliseconds
            }

            if ( $returnVar === 0 ) {
                Util::logDebug( 'Successfully unzipped file to: ' . $destination );

                return ['success' => true, 'numFiles' => $numFiles];
            }
            else {
                Util::logError( 'Failed to unzip file. Command return value: ' . $returnVar );

                return ['error' => 'Failed to unzip file', 'numFiles' => $numFiles];
            }
        }
        else {
            Util::logError( 'Failed to open process for command: ' . $command );

            return ['error' => 'Failed to open process', 'numFiles' => $numFiles];
        }
    }

    /**
     * Fetches a file from a given URL and saves it to a specified file path.
     *
     * This method attempts to retrieve the content from the provided URL and save it to the specified file path.
     * If any error occurs during fetching or saving, it logs the error and returns an error message.
     * If the operation is successful, it returns the file path.
     * The method also logs the file size if the input stream is a valid resource.
     *
     * @param   string  $moduleUrl    The URL from which to fetch the file content.
     * @param   string  $filePath     The path where the file content should be saved.
     * @param   bool    $progressBar  Optional. Whether to display a progress bar during the download process. Default is false.
     *
     * @return array Returns the file path if successful, or an array with an error message if an error occurs.
     */
    public function getFileFromUrl(string $moduleUrl, string $filePath, $progressBar = false)
    {
        // Open the URL for reading
        $inputStream = @fopen( $moduleUrl, 'rb' );
        if ( $inputStream === false ) {
            Util::logError( 'Error fetching content from URL: ' . $moduleUrl );

            return ['error' => 'Error fetching module'];
        }

        // Open the file for writing
        $outputStream = @fopen( $filePath, 'wb' );
        if ( $outputStream === false ) {
            Util::logError( 'Error opening file for writing: ' . $filePath );
            fclose( $inputStream );

            return ['error' => 'Error saving module'];
        }

        // Read and write in chunks to avoid memory overload
        $bufferSize = 8096; // 8KB
        $chunksRead = 0;

        while ( !feof( $inputStream ) ) {
            $buffer = fread( $inputStream, $bufferSize );
            fwrite( $outputStream, $buffer );
            $chunksRead++;

            // Send progress update
            if ( $progressBar ) {
                $progress = $chunksRead;
                echo json_encode( ['progress' => $progress] );

                // Check if output buffering is active before calling ob_flush()
                if ( ob_get_length() !== false ) {
                    ob_flush();
                }
                flush();
            }
        }

        fclose( $inputStream );
        fclose( $outputStream );

        return ['success' => true];
    }
}
