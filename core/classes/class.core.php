<?php
/*
 *
 *  * Copyright (c) 2021-2026 Bearsampp
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

    const PHP_EXE = 'php-win.exe';
    const SETENV_EXE = 'SetEnv.exe';
    const NSSM_EXE = 'nssm.exe';
    const OPENSSL_EXE = 'openssl.exe';
    const OPENSSL_CONF = 'openssl.cfg';
    const HOSTSEDITOR_EXE = 'hEdit_x64.exe';
    const LN_EXE = 'ln.exe';
    const PWGEN_EXE = "PwTech.exe";

    const APP_VERSION = 'version.dat';
    const LAST_PATH = 'lastPath.dat';
    const EXEC = 'exec.dat';
    const LOADING_PID = 'loading.pid';

    /**
     * Core constructor.
     *
     * Loads the WinBinder extension if available.
     */
    public function __construct()
    {
        if (extension_loaded('winbinder')) {
            require_once Path::getLibsPath() . '/winbinder/winbinder.php';
        }
    }

    /**
     * Retrieves the application version.
     *
     * @return string|null The application version or null if not found.
     */
    public function getAppVersion()
    {
        global $bearsamppLang;

        $filePath = Path::getResourcesPath() . '/' . self::APP_VERSION;
        if (!is_file($filePath)) {
            Log::error(sprintf($bearsamppLang->getValue(Lang::ERROR_CONF_NOT_FOUND), APP_TITLE, $filePath));

            return null;
        }

        return trim(file_get_contents($filePath));
    }

    /**
     * Retrieves the content of the last path file.
     *
     * @return string|false The content of the last path file or false on failure.
     */
    public function getLastPathContent()
    {
        return @file_get_contents(Path::getLastPath());
    }

    /**
     * Retrieves the content of the exec file without unlinking it.
     *
     * @return string|false The content of the exec file or false if it doesn't exist.
     */
    public function getPreviousExec()
    {
        $file = $this->getExec();
        if (file_exists($file)) {
            return trim(file_get_contents($file));
        }

        return false;
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
        return Path::getTmpPath($aetrayPath) . '/' . self::EXEC;
    }

    /**
     * Sets the content of the exec file.
     *
     * @param   string  $action  The content to set in the exec file.
     */
    public function setExec($action)
    {
        file_put_contents($this->getExec(), $action);
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
        return Path::getResourcesPath($aetrayPath) . '/' . self::LOADING_PID;
    }

    /**
     * Adds a PID to the loading PID file.
     *
     * @param   int  $pid  The PID to add.
     */
    public function addLoadingPid($pid)
    {
        file_put_contents($this->getLoadingPid(), $pid . PHP_EOL, FILE_APPEND);
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
     * It first validates the archive's entry paths (and counts its files) via a structured
     * listing, then proceeds with the extraction while providing progress updates via a
     * callback function.
     *
     * @param   string         $filePath          The path to the zip file.
     * @param   string         $destination       The directory to extract the files to.
     * @param   callable|null  $progressCallback  A callback function to report progress. The callback receives two parameters:
     *                                            - int $currentFile: The current file number being extracted.
     *                                            - int $totalFiles: The total number of files to be extracted.
     *
     * @return array|false An array containing the result of the extraction on success or failure:
     *                     - On success: ['success' => true, 'numFiles' => int]
     *                     - On failure: ['error' => string, 'numFiles' => int]
     *                     - Returns false if the 7-Zip executable is not found.
     * @global  object         $bearsamppRoot     Global object to get core paths.
     *
     */
    public function unzipFile($filePath, $destination, $progressCallback = null)
    {
        global $bearsamppRoot;

        $sevenZipPath = Path::getLibsPath() . '/7zip/7za.exe';

        if (!file_exists($sevenZipPath)) {
            Log::error('7za.exe not found at: ' . $sevenZipPath);

            return false;
        }

        if ($progressCallback) {
            call_user_func($progressCallback, 'Analyzing archive...');
        }

        // Defensive path-traversal scan: parse the structured `7z l -slt` output into
        // each individual entry path and validate every one relative to the destination,
        // so extraction can never escape the intended directory. This must fail closed:
        // any listing/parse failure aborts the operation rather than proceeding unverified.
        // The scan also returns the number of archive entries, used as the extraction
        // file count. A standalone integrity test is not needed: the archive's SHA-256
        // was already verified against its .sha256 sidecar upstream, and `7za x`
        // CRC-checks every file as it is extracted.
        $numFiles = self::isSafeDestinationFileList($sevenZipPath, $filePath);
        if ($numFiles === false) {
            Log::error('Archive path-traversal scan failed or rejected for: ' . $filePath);

            return false;
        }
        Log::trace('Number of files to be extracted: ' . $numFiles);

        if ($progressCallback) {
            call_user_func($progressCallback, 'Initializing extraction...');
        }
        // Extract the archive, streaming progress line-by-line
        $returnVar = CommandRunner::stream(
            $sevenZipPath,
            ['x', $filePath, '-y', '-bsp1', '-bb0', '-o' . $destination],
            function (string $line) use ($progressCallback) {
                Log::trace("Processing line: $line");
                if ($line === 'Everything is Ok') {
                    if ($progressCallback) {
                        Log::trace('Extraction progress: 100%');
                        call_user_func($progressCallback, 100);
                    }
                } elseif ($progressCallback && preg_match('/(?:^|\s)(\d+)%/', $line, $matches)) {
                    $currentPercentage = intval($matches[1]);
                    Log::trace("Extraction progress: $currentPercentage%");
                    call_user_func($progressCallback, $currentPercentage);
                } else {
                    Log::trace("Line did not match pattern: $line");
                }
            }
        );

        if ($returnVar === false) {
            Log::error('Failed to open process for: ' . $sevenZipPath);

            return ['error' => 'Failed to open process', 'numFiles' => $numFiles];
        }

        Log::trace('Command return value: ' . $returnVar);

        if ($returnVar === 0 && $progressCallback) {
            Log::trace('Extraction completed successfully. Setting progress to 100%');
            call_user_func($progressCallback, 100);
            usleep(100000); // 100 milliseconds
        }

        if ($returnVar === 0) {
            Log::debug('Successfully unzipped file to: ' . $destination);

            return ['success' => true, 'numFiles' => $numFiles];
        }

        Log::error('Failed to unzip file. Command return value: ' . $returnVar);

        return ['error' => 'Failed to unzip file', 'numFiles' => $numFiles];
    }

    /**
     * Parses `7z l -slt` output and verifies every listed entry path is safe to
     * extract relative to the destination, returning the number of valid entries.
     *
     * The listing is captured to a temp file (avoiding a slow pipe capture) and split
     * into one block per entry on blank-line separators, each block containing a
     * `Path = <value>` line. We extract each entry path individually and reject any
     * that is empty, is absolute (drive-letter or rooted path), or contains a
     * parent-directory (`..`) traversal. Banner/header blocks (including the header's
     * reference to the archive itself) are skipped, and directories are validated but
     * not counted, matching the `Files:` count of `7z t`. This must fail closed: a
     * failure to list the archive, or discovering no file entries or any unsafe entry,
     * returns false so extraction is aborted rather than performed on an unverified
     * archive.
     *
     * @param   string  $sevenZipPath  Path to the 7za executable.
     * @param   string  $filePath      Path to the archive file.
     *
     * @return  int|false              The number of listed (safe) file entries on success,
     *                                 or false if the listing failed or any entry is unsafe.
     */
    private static function isSafeDestinationFileList($sevenZipPath, $filePath)
    {
        $listingFile = Path::getTmpPath() . '/7z-listing-' . uniqid('', true) . '.txt';
        $stderrFile  = Path::getTmpPath() . '/7z-listing-' . uniqid('', true) . '.err';

        $returnVar = CommandRunner::execToFile($sevenZipPath, ['l', '-slt', $filePath], $listingFile, $stderrFile);
        if ($returnVar === false) {
            Log::error('Path-traversal scan: unable to list archive: ' . $filePath);

            return false;
        }

        $listingOutput = @file_get_contents($listingFile);
        @unlink($listingFile);
        @unlink($stderrFile);

        if (!is_string($listingOutput) || $listingOutput === '') {
            Log::error('Path-traversal scan: unable to list archive: ' . $filePath);

            return false;
        }

        // 7-Zip separates the `-slt` entry blocks with blank lines (a single dash
        // line precedes the first entry, and the banner/header references the archive
        // itself). Normalize line endings and split on blank lines so each block is
        // one archive entry.
        $listingOutput = str_replace("\r\n", "\n", $listingOutput);
        $blocks        = preg_split('/\n[ \t]*\n/', $listingOutput);
        if (empty($blocks)) {
            // No entries listed at all - cannot validate, fail closed.
            Log::error('Path-traversal scan: no entries found in archive: ' . $filePath);

            return false;
        }

        // The header block's `Path = <archive itself>` entry must not be validated
        // or counted; skip any block that resolves to the archive file.
        $archivePathNormalized = strtolower(str_replace('/', '\\', $filePath));

        $numFiles = 0;
        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            if (!preg_match('/^Path = (.*)$/m', $block, $match)) {
                // Not an entry block (banner/scanning preamble lines) - skip.
                continue;
            }

            $entryPath = trim($match[1]);
            if (strtolower(str_replace('/', '\\', $entryPath)) === $archivePathNormalized) {
                // Banner/header (and any trailing archive-summary) block.
                continue;
            }

            if (self::isUnsafeArchiveEntryPath($entryPath)) {
                Log::error('Archive contains an unsafe entry path ("' . $entryPath . '"): ' . $filePath);

                return false;
            }

            // Directories are validated above but not counted as files, matching the
            // file count previously reported by `7z t` (`Files:` excludes folders).
            if (preg_match('/^Attributes = D\s*$/m', $block)) {
                continue;
            }

            $numFiles++;
        }

        if ($numFiles === 0) {
            // Nothing to extract - cannot validate, fail closed.
            Log::error('Path-traversal scan: no file entries found in archive: ' . $filePath);

            return false;
        }

        return $numFiles;
    }

    /**
     * Determines whether a single archive entry path is unsafe to extract.
     *
     * A path is considered unsafe if it is empty, contains a parent-directory (`..`)
     * token, or is absolute (a Windows drive path such as `C:\...` or a rooted path
     * beginning with `/` or `\`).
     *
     * @param   string  $entryPath  The entry path extracted from the archive listing.
     *
     * @return  bool                True if the path is unsafe, false if safe.
     */
    private static function isUnsafeArchiveEntryPath($entryPath)
    {
        if ($entryPath === '') {
            return true;
        }

        // Parent-directory traversal (matches any path segment equal to '..').
        if (preg_match('/(?:^|[\\/\\\\])\.\.(?:[\\/\\\\]|$)/', $entryPath)) {
            return true;
        }

        // Absolute / rooted paths.
        if (preg_match('#^(?:[A-Za-z]:[\\/\\\\]|[\\/\\\\])#', $entryPath)) {
            return true;
        }

        return false;
    }
}
