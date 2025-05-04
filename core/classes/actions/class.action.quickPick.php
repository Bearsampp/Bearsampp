<?php
/*
 *
 *  * Copyright (c) 2022-2025 Bearsampp
 *  * License: GNU General Public License version 3 or later; see LICENSE.txt
 *  * Website: https://bearsampp.com
 *  * Github: https://github.com/Bearsampp
 *
 */

/**
 * Class QuickPick
 *
 * The QuickPick class provides functionalities for managing and installing various modules
 * within the Bearsampp application. It includes methods for retrieving available modules,
 * fetching module versions, parsing release properties, and validating license keys.
 */
class QuickPick
{
    /**
     * @var array $modules
     *
     * An associative array where the key is the module name and the value is an array containing the module type.
     * The module type can be one of the following:
     * - 'application'
     * - 'binary'
     * - 'tool'
     */
    public $modules = [
        'Adminer'     => ['type' => 'application'],
        'Apache'      => ['type' => 'binary'],
        'Bruno'       => ['type' => 'tools'],
        'Composer'    => ['type' => 'tools'],
        'ConsoleZ'    => ['type' => 'tools'],
        'Ghostscript' => ['type' => 'tools'],
        'Git'         => ['type' => 'tools'],
        'Mailpit'     => ['type' => 'binary'],
        'MariaDB'     => ['type' => 'binary'],
        'Memcached'   => ['type' => 'binary'],
        'MySQL'       => ['type' => 'binary'],
        'Ngrok'       => ['type' => 'tools'],
        'NodeJS'      => ['type' => 'binary'],
        'Perl'        => ['type' => 'tools'],
        'PHP'         => ['type' => 'binary'],
        'PhpMyAdmin'  => ['type' => 'application'],
        'PhpPgAdmin'  => ['type' => 'application'],
        'PostgreSQL'  => ['type' => 'binary'],
        'Python'      => ['type' => 'tools'],
        'Ruby'        => ['type' => 'tools'],
        'Xlight'      => ['type' => 'binary']
    ];

    /**
     * @var array $versions
     *
     * An associative array where the key is the module name and the value is an array containing the module versions.
     */
    private $versions = [];

    /**
     * @var string $jsonFilePath
     *
     * The file path to the local quickpick-releases.json file.
     */
    private $jsonFilePath;

    /**
     * Constructor to initialize the jsonFilePath.
     */
    public function __construct()
    {
        global $bearsamppCore;
        $this->jsonFilePath = $bearsamppCore->getResourcesPath() . '/quickpick-releases.json';
    }

    /**
     * Format version label with PR indicator if it's a prerelease
     *
     * @param string $version The version to format
     * @param bool $isPrerelease Whether this version is a prerelease
     * @return string Formatted version string
     */
    private function formatVersionLabel($version, $isPrerelease = false) {
        global $bearsamppConfig;
        $includePr = $bearsamppConfig->getIncludePr();

        if ($isPrerelease && $includePr == 1) {
            return '<span class="text-danger">' . htmlspecialchars($version) . ' PR</span>';
        }

        return htmlspecialchars($version);
    }

    /**
     * Retrieves the list of available modules.
     *
     * @return array An array of module names.
     */
    public function getModules(): array
    {
        return array_keys( $this->modules );
    }

    /**
     * Loads the QuickPick interface with the available modules and their versions.
     *
     * @param   string  $imagesPath  The path to the images directory.
     *
     * @return string The HTML content of the QuickPick interface.
     *
     * @throws Exception
     */
    public function loadQuickpick(string $imagesPath): string
    {
        $this->checkQuickpickJson();

        $modules  = $this->getModules();
        $versions = $this->getVersions();

        return $this->getQuickpickMenu( $modules, $versions, $imagesPath );
    }

    /**
     * Checks if the local `quickpick-releases.json` file is up-to-date with the remote version.
     *
     * Compares the creation time of the local JSON file with the remote file's last modified time.
     * If the remote file is newer or the local file does not exist, it fetches the latest JSON data by calling
     * the `rebuildQuickpickJson` method.
     *
     * @return array|false Returns the JSON data if the remote file is newer or the local file does not exist,
     *                     otherwise returns false.
     * @throws Exception
     */
    public function checkQuickpickJson()
    {
        global $bearsamppConfig;

        // Determine local file creation time or rebuild if missing
        $localFileCreationTime = $this->getLocalFileCreationTime();

        // Attempt to retrieve remote file headers
        $headers = get_headers(QUICKPICK_JSON_URL, 1);
        if (!$this->isValidHeaderResponse($headers)) {
            // If headers or Date are invalid, assume no update needed
            return false;
        }

        // Optionally log headers for verbose output
        $this->logHeaders($headers);

        // Compare the creation times (remote vs. local)
        $remoteFileCreationTime = strtotime(isset($headers['Date']) ? $headers['Date'] : '');
		if ($remoteFileCreationTime > $localFileCreationTime) { return $this->rebuildQuickpickJson(); }

        // Return false if local file is already up-to-date
        return false;
    }

    /**
     * Returns the local file's creation time, or triggers and returns 0 if file does not exist.
     *
     * @return int Local file's creation time or 0 if the file doesn't exist.
     */
    private function getLocalFileCreationTime()
    {
        if (!file_exists($this->jsonFilePath)) {
            // If local file is missing, rebuild it immediately
            $this->rebuildQuickpickJson();
            return 0;
        }
        return filectime($this->jsonFilePath);
    }

    /**
     * Determines whether the header response is valid and includes a 'Date' key.
     *
     * @param mixed $headers Headers retrieved from get_headers().
     * @return bool True if headers are valid and contain 'Date', false otherwise.
     */
    private function isValidHeaderResponse($headers): bool
    {
        // If headers retrieval failed or Date is not set, return false
        if ($headers === false || !isset($headers['Date'])) {
            return false;
        }
        return true;
    }

    /**
     * Logs the headers in debug mode if logsVerbose is set to 2.
     *
     * @param array $headers The headers returned by get_headers().
     */
    private function logHeaders(array $headers): void
    {
        global $bearsamppConfig;

        if ($bearsamppConfig->getLogsVerbose() === 2) {
            Util::logDebug('Headers: ' . print_r($headers, true));
        }
    }

    /**
     * Retrieves the QuickPick JSON data from the local file.
     *
     * @return array The decoded JSON data, or an error message if the file cannot be fetched or decoded.
     */
    public function getQuickpickJson(): array
    {
        $content = @file_get_contents( $this->jsonFilePath );
        if ( $content === false ) {
            Util::logError( 'Error fetching content from JSON file: ' . $this->jsonFilePath );

            return ['error' => 'Error fetching JSON file'];
        }

        $data = json_decode( $content, true );
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            Util::logError( 'Error decoding JSON content: ' . json_last_error_msg() );

            return ['error' => 'Error decoding JSON content'];
        }

        return $data;
    }

    /**
     * Rebuilds the local quickpick-releases.json file by fetching the latest data from the remote URL.
     *
     * @return array An array containing the status and message of the rebuild process.
     * @throws Exception If the JSON content cannot be fetched or saved.
     */
    public function rebuildQuickpickJson(): array
    {
        Util::logDebug( 'Fetching JSON file: ' . $this->jsonFilePath );

        // Fetch the JSON content from the URL
        $jsonContent = file_get_contents( QUICKPICK_JSON_URL );

        if ( $jsonContent === false ) {
            // Handle error if the file could not be fetched
            throw new Exception( 'Failed to fetch JSON content from the URL.' );
        }

        // Save the JSON content to the specified path
        $result = file_put_contents( $this->jsonFilePath, $jsonContent );

        if ( $result === false ) {
            // Handle error if the file could not be saved
            throw new Exception( 'Failed to save JSON content to the specified path.' );
        }

        // Return success message
        return ['success' => 'JSON content fetched and saved successfully'];
    }

    /**
     * Retrieves the list of available versions for all modules.
     *
     * This method fetches the QuickPick JSON data and returns an array of versions or If no versions are found, an error
     * message is logged and returned.
     *
     * @return array An array of version strings for the specified module, or an error message if no versions are found.
     */
    public function getVersions(): array
    {
        Util::logDebug( 'Versions called' );

        $versions = [];

        $jsonData = $this->getQuickpickJson();

        foreach ( $jsonData as $entry ) {
            if ( is_array( $entry ) ) {
                if ( isset( $entry['module'] ) && is_string( $entry['module'] ) ) {
                    if ( isset( $entry['versions'] ) && is_array( $entry['versions'] ) ) {
                        $versions[$entry['module']] = array_column( $entry['versions'], null, 'version' );
                    }
                }
            }
            else {
                Util::logError( 'Invalid entry format in JSON data' );
            }
        }

        if ( empty( $versions ) ) {
            Util::logError( 'No versions found' );

            return ['error' => 'No versions found'];
        }

        Util::logDebug( 'Found versions' );

        $this->versions = $versions;

        return $versions;
    }

    /**
     * Fetches the URL of a specified module version from the local quickpick-releases.json file.
     *
     * This method reads the quickpick-releases.json file to find the URL associated with the given module
     * and version. It logs the process and returns the URL if found, or an error message if not.
     *
     * @param   string  $module   The name of the module.
     * @param   string  $version  The version of the module.
     *
     * @return string|array The URL of the specified module version or an error message if the version is not found.
     */
    public function getModuleUrl(string $module, string $version)
    {
        $this->getVersions();
        Util::logDebug( 'getModuleUrl called for module: ' . $module . ' version: ' . $version );
        $url = trim( $this->versions['module-' . strtolower( $module )][$version]['url'] );
        if ( $url <> '' ) {
            Util::logDebug( 'Found URL for version: ' . $version . ' URL: ' . $url );

            return $url;
        }
        else {
            Util::logError( 'Version not found: ' . $version );

            return ['error' => 'Version not found'];
        }
    }

    /**
     * Validates the format of a given username key by checking it against an external API.
     *
     * This method performs several checks to ensure the validity of the username key:
     * 1. Logs the method call.
     * 2. Ensures the global configuration is available.
     * 3. Retrieves the username key from the global configuration.
     * 4. Ensures the username key is not empty.
     * 5. Constructs the API URL using the username key.
     * 6. Fetches the API response.
     * 7. Decodes the JSON response.
     * 8. Validates the response data.
     *
     * @return bool True if the username key is valid, false otherwise.
     */
    public function checkDownloadId(): bool
    {
        global $bearsamppConfig;

        Util::logDebug( 'checkDownloadId method called.' );

        // Ensure the global config is available
        if ( !isset( $bearsamppConfig ) ) {
            Util::logError( 'Global configuration is not set.' );

            return false;
        }

        $DownloadId = $bearsamppConfig->getDownloadId();
        Util::logDebug( 'DownloadId is: ' . $DownloadId );

        // Ensure the license key is not empty
        if ( empty( $DownloadId ) ) {
            Util::logError( 'License key is empty.' );

            return false;
        }

        $url = QUICKPICK_API_URL . QUICKPICK_API_KEY . '&download_id=' . $DownloadId;
        Util::logDebug( 'API URL: ' . $url );

        $response = @file_get_contents( $url );

        // Check if the response is false
        if ( $response === false ) {
            $error = error_get_last();
            Util::logError( 'Error fetching API response: ' . $error['message'] );

            return false;
        }

        Util::logDebug( 'API response: ' . $response );

        $data = json_decode( $response, true );

        // Check if the JSON decoding was successful
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            Util::logError( 'Error decoding JSON response: ' . json_last_error_msg() );

            return false;
        }

        // Validate the response data
        if ( isset( $data['success'] ) && $data['success'] === true && isset( $data['data'] ) && is_array( $data['data'] ) && count( $data['data'] ) > 0 ) {
            Util::logDebug( 'License key valid: ' . $DownloadId );

            return true;
        }

        Util::logError( 'Invalid license key: ' . $DownloadId );

        return false;
    }

    /**
     * Installs a specified module by fetching its URL and unzipping its contents.
     *
     * This method retrieves the URL of the specified module and version from the QuickPick JSON data.
     * If the URL is found, it fetches and unzips the module. If the URL is not found, it logs an error
     * and returns an error message.
     *
     * @param   string  $module   The name of the module to install.
     * @param   string  $version  The version of the module to install.
     *
     * @return array An array containing the status and message of the installation process.
     *               If successful, it returns the response from the fetchAndUnzipModule method.
     *               If unsuccessful, it returns an error message indicating the issue.
     */
    public function installModule(string $module, string $version): array
    {
        // Find the module URL and module name from the data
        $moduleUrl = $this->getModuleUrl( $module, $version );

        if ( is_array( $moduleUrl ) && isset( $moduleUrl['error'] ) ) {
            Util::logError( 'Module URL not found for module: ' . $module . ' version: ' . $version );

            return ['error' => 'Module URL not found'];
        }

        if ( empty( $moduleUrl ) ) {
            Util::logError( 'Module URL not found for module: ' . $module . ' version: ' . $version );

            return ['error' => 'Module URL not found'];
        }

        $state = Util::checkInternetState();
        if ( $state ) {
            $response = $this->fetchAndUnzipModule( $moduleUrl, $module );
            Util::logDebug( 'Response is: ' . print_r( $response, true ) );

            return $response;
        }
        else {
            Util::logError( 'No internet connection available.' );

            return ['error' => 'No internet connection'];
        }
    }

    /**
     * Fetches the module URL and stores it in /tmp, then unzips the file based on its extension.
     *
     * @param   string  $moduleUrl  The URL of the module to fetch.
     * @param   string  $module     The name of the module.
     *
     * @return array An array containing the status and message.
     */
    public function fetchAndUnzipModule(string $moduleUrl, string $module): array
{
    Util::logDebug("$module is: " . $module);

    global $bearsamppRoot, $bearsamppCore;
    $tmpDir = $bearsamppRoot->getTmpPath();
    Util::logDebug('Temporary Directory: ' . $tmpDir);

    $fileName = basename($moduleUrl);
    Util::logDebug('File Name: ' . $fileName);

    $tmpFilePath = $tmpDir . '/' . $fileName;
    Util::logDebug('File Path: ' . $tmpFilePath);

    $moduleName = str_replace('module-', '', $module);
    Util::logDebug('Module Name: ' . $moduleName);

    $moduleType = $this->modules[$module]['type'];
    Util::logDebug('Module Type: ' . $moduleType);

    // Get module type
    $destination = $this->getModuleDestinationPath($moduleType, $moduleName);
    Util::logDebug('Destination: ' . $destination);

    // Retrieve the file path from the URL using the bearsamppCore module,
    // passing the module URL and temporary file path, with the use Progress Bar parameter set to true.
    $result = $bearsamppCore->getFileFromUrl($moduleUrl, $tmpFilePath, true);

    // Check if $result is false
    if ($result === false) {
        Util::logError('Failed to retrieve file from URL: ' . $moduleUrl);
        return ['error' => 'Failed to retrieve file from URL'];
    }

    // Determine the file extension and call the appropriate unzipping function
    $fileExtension = pathinfo($tmpFilePath, PATHINFO_EXTENSION);
    Util::logDebug('File extension: ' . $fileExtension);

    if ($fileExtension === '7z' || $fileExtension === 'zip') {
        // Send phase indicator for extraction
        echo json_encode(['phase' => 'extracting']);
        if (ob_get_length()) {
            ob_flush();
        }
        flush();

        $unzipResult = $bearsamppCore->unzipFile($tmpFilePath, $destination, function ($currentPercentage) {
            echo json_encode(['progress' => "$currentPercentage%"]);
            if (ob_get_length()) {
                ob_flush();
            }
            flush();
        });

        if ($unzipResult === false) {
            return ['error' => 'Failed to unzip file. File: ' . $tmpFilePath . ' could not be unzipped', 'Destination: ' . $destination];
        }
    } else {
        Util::logError('Unsupported file extension: ' . $fileExtension);
        return ['error' => 'Unsupported file extension'];
    }

    return ['success' => 'Module installed successfully'];
}

    /**
     * Get the destination path for a given module type and name.
     *
     * This method constructs the destination path based on the type of module
     * (application, binary, or tools) and the module name. It utilizes the
     * `bearsamppRoot` global object to retrieve the base paths for each module type.
     *
     * @param   string  $moduleType  The type of the module ('application', 'binary', or 'tools').
     * @param   string  $moduleName  The name of the module.
     *
     * @return string The constructed destination path for the module.
     */
    public function getModuleDestinationPath(string $moduleType, string $moduleName)
    {
        global $bearsamppRoot;
        if ( $moduleType === 'application' ) {
            $destination = $bearsamppRoot->getAppsPath() . '/' . strtolower( $moduleName ) . '/';
        }
        elseif ( $moduleType === 'binary' ) {
            $destination = $bearsamppRoot->getBinPath() . '/' . strtolower( $moduleName ) . '/';
        }
        elseif ( $moduleType === 'tools' ) {
            $destination = $bearsamppRoot->getToolsPath() . '/' . strtolower( $moduleName ) . '/';
        }
        else {
            $destination = '';
        }

        return $destination;
    }

    /**
     * Generates the HTML content for the QuickPick menu.
     *
     * This method creates the HTML structure for the QuickPick interface, including a dropdown
     * for selecting modules and their respective versions. It checks if the license key is valid
     * before displaying the modules. If the license key is invalid, it displays a subscription prompt.
     * If there is no internet connection, it displays a message indicating the lack of internet.
     *
     * @param   array   $modules     An array of available modules.
     * @param   array   $versions    An associative array where the key is the module name and the value is an array containing the module versions.
     * @param   string  $imagesPath  The path to the images directory.
     *
     * @return string The HTML content of the QuickPick menu.
     */
    public function getQuickpickMenu(array $modules, array $versions, string $imagesPath): string
    {
        global $bearsamppConfig;
        $includePr = $bearsamppConfig->getIncludePr();
        
        ob_start();
        if ( Util::checkInternetState() ) {

            // Check if the license key is valid
            if ( $this->checkDownloadId() ): ?>
                <div id = 'quickPickContainer'>
                    <div class = 'quickpick me-5'>

                        <div class = "custom-select">
                            <button class = "select-button" role = "combobox"
                                    aria-label = "select button"
                                    aria-haspopup = "listbox"
                                    aria-expanded = "false"
                                    aria-controls = "select-dropdown">
                                <span class = "selected-value">Select a module and version</span>
                                <span class = "arrow"></span>
                            </button>
                            <ul class = "select-dropdown" role = "listbox" id = "select-dropdown">

                                <?php
                                foreach ( $modules as $module ): ?>
                                    <?php if ( is_string( $module ) ): ?>
                                        <li role = "option" class = "moduleheader">
                                            <?php echo htmlspecialchars( $module ); ?>
                                        </li>

                                        <?php
                                        foreach ( $versions['module-' . strtolower( $module )] as $version_array ): 
                                            // Skip prerelease versions if includePr is not enabled
                                            if (isset($version_array['prerelease']) && $version_array['prerelease'] === true && $includePr != 1) {
                                                continue;
                                            }
                                        ?>
                                            <li role = "option" class = "moduleoption"
                                                id = "<?php echo htmlspecialchars( $module ); ?>-version-<?php echo htmlspecialchars( $version_array['version'] ); ?>-li">
                                                <input type = "radio"
                                                       id = "<?php echo htmlspecialchars( $module ); ?>-version-<?php echo htmlspecialchars( $version_array['version'] ); ?>"
                                                       name = "module" data-module = "<?php echo htmlspecialchars( $module ); ?>"
                                                       data-value = "<?php echo htmlspecialchars( $version_array['version'] ); ?>">
                                                <label
                                                    for = "<?php echo htmlspecialchars( $module ); ?>-version-<?php echo htmlspecialchars( $version_array['version'] ); ?>"><?php echo $this->formatVersionLabel( $version_array['version'], isset($version_array['prerelease']) && $version_array['prerelease'] === true ); ?></label>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class = "progress " id = "progress" tabindex = "-1" style = "width:260px;display:none"
                         aria-labelledby = "progressbar" aria-hidden = "true">
                        <div class = "progress-bar progress-bar-striped progress-bar-animated" id = "progress-bar" role = "progressbar" aria-valuenow = "0" aria-valuemin = "0"
                             aria-valuemax = "100" data-module = "Module"
                             data-version = "0.0.0">0%
                        </div>
                        <div id = "download-module" style = "display: none">ModuleName</div>
                        <div id = "download-version" style = "display: none">Version</div>
                    </div>
                </div>
            <?php else: ?>
                <div id = "subscribeContainer" class = "text-center mt-3 pe-3">
                    <a href = "<?php echo Util::getWebsiteUrl( 'subscribe' ); ?>" class = "btn btn-dark d-inline-flex align-items-center">
                        <img src = "<?php echo $imagesPath . 'subscribe.svg'; ?>" alt = "Subscribe Icon" class = "me-2">
                        Subscribe to QuickPick now
                    </a>
                </div>
            <?php endif;
        }
        else {
            ?>
            <div id = "InternetState" class = "text-center mt-3 pe-3">
                <img src = "<?php echo $imagesPath . 'no-wifi-icon.svg'; ?>" alt = "No Wifi Icon" class = "me-2">
                <span>No internet present</span>
            </div>
            <?php
        }

        return ob_get_clean();
    }
}
