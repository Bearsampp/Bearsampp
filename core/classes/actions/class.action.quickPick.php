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

    public function createQuickPickLive()
    {
        Util::logDebug('Starting createQuickPickLive method to fetch and save prerelease information');

        // Counter to track how many prerelease versions were found and saved
        $totalPrereleasesSaved = 0;
        $totalPrereleasesFound = 0;
        $totalPrereleasesRemoved = 0;

        // First, get all current modules and their versions from the JSON file
        $currentData = $this->getQuickpickJson();
        $currentPrereleases = [];

        // Build a map of all current prerelease versions
        foreach ($currentData as $key => $entry) {
            if (is_array($entry) && isset($entry['module']) && isset($entry['versions']) && is_array($entry['versions'])) {
                $moduleKey = $entry['module'];
                $currentPrereleases[$moduleKey] = [];

                foreach ($entry['versions'] as $version) {
                    if (isset($version['prerelease']) && $version['prerelease'] === true) {
                        $currentPrereleases[$moduleKey][] = $version['version'];
                    }
                }
            }
        }

        // Build list of repos
        $modules = $this->getModules();
        $actualPrereleases = [];

        foreach ($modules as $moduleName) {
            Util::logTrace('Creating QuickPickLive module ' . $moduleName);
            $moduleKey = 'module-' . strtolower($moduleName);
            $actualPrereleases[$moduleKey] = [];

            // Create repo url
            $url = 'https://api.github.com/repos/' . APP_GITHUB_USER . '/module-' . $moduleName . '/releases';
            Util::logTrace('Quickpick Live URL: ' . $url);

            // for each repos use the API and retrieve the list of pre-releases
            // Use curl to retrieve a list of all releases in the module
            $json = Util::getApiJson($url);

			// Validate that we received a response
            if (empty($json)) {
                Util::logError('Empty response from API for module: ' . $moduleName);
                continue;
            }

			// Validate JSON before decoding
            $data = json_decode($json, true);
            if ($data === null || json_last_error() !== JSON_ERROR_NONE) {
                Util::logError('Failed to decode JSON data for module: ' . $moduleName . '. Error: ' . json_last_error_msg());
                continue;
            }

            // Filter for pre-release only
            $prereleases = array_filter($data, function ($release) {
                return isset($release['prerelease']) && $release['prerelease'] === true;
            });

            $modulePrereleasesCount = count($prereleases);
            $totalPrereleasesFound += $modulePrereleasesCount;
            Util::logDebug("Found $modulePrereleasesCount prerelease(s) for module $moduleName");

            // Now $prereleases contains only prerelease objects
            foreach ($prereleases as $release) {
                // Log release info for debugging
                $releaseName = isset($release['name']) ? $release['name'] : 'unnamed';
                $releaseTag = isset($release['tag_name']) ? $release['tag_name'] : 'no-tag';
                Util::logTrace("Processing prerelease: $releaseName (tag: $releaseTag)");

                // Each release may have multiple assets
                if (isset($release['assets']) && is_array($release['assets'])) {
                    $assetCount = count($release['assets']);
                    Util::logTrace("Release has $assetCount asset(s)");

                    foreach ($release['assets'] as $asset) {
                        if (isset($asset['name']) && substr($asset['name'], -3) === '.7z') {
                            // This asset is a .7z file
                            $downloadUrl = $asset['browser_download_url'];
                            $fileName    = $asset['name'];
                            Util::logTrace("Processing asset: $fileName");

                            // Strip leading url
                            $pattern          = '/^.*' . preg_quote($moduleName, '/') . '-/i';
                            $strippedFileName = preg_replace($pattern, '', $fileName);

                            // $strippedFileName is something like "3.10.9-2022.4.30.7z"
                            if (preg_match('/^([^-]+)-\d{4}\.\d{1,2}\.\d{1,2}\.7z$/', $strippedFileName, $matches)) {
                                $versionOnly = $matches[1]; // This will be "3.10.9"
                                // Now you can use $versionOnly as needed
                                Util::logDebug("Found version: $versionOnly ($downloadUrl)");

                                // Add to the list of actual prereleases
                                $actualPrereleases[$moduleKey][] = $versionOnly;

                                // Update the quickpick-releases.json file with this version
                                // Since this is coming from a pre-release, set the prerelease flag to true
                                $result = $this->updateQuickpickReleasesJson($moduleName, $versionOnly, $downloadUrl, true);
                                if ($result) {
                                    $totalPrereleasesSaved++;
                                    Util::logDebug("Successfully saved prerelease version $versionOnly for module $moduleName to quickpick-releases.json");
                                } else {
                                    Util::logDebug("Failed to save or already exists: prerelease version $versionOnly for module $moduleName");
                                }
                            } else {
                                Util::logDebug("Asset filename doesn't match expected pattern: $strippedFileName");
                            }
                        } else {
                            $assetName = isset($asset['name']) ? $asset['name'] : 'unnamed';
                            Util::logTrace("Skipping non-.7z asset: $assetName");
                        }
                    }
                } else {
                    Util::logDebug("No assets found for release: $releaseName");
                }
            }
            // Next Module
        }

        // Now check for versions that were previously marked as prereleases but are no longer prereleases
        foreach ($currentPrereleases as $moduleKey => $versions) {
            $moduleName = str_replace('module-', '', $moduleKey);

            foreach ($versions as $version) {
                // If this version is not in the actual prereleases list, completely remove it from the file
                if (!isset($actualPrereleases[$moduleKey]) || !in_array($version, $actualPrereleases[$moduleKey])) {
                    Util::logDebug("Version $version of module $moduleName is no longer a prerelease, removing it from the file");
                    $this->removeVersionFromJson($moduleName, $version);
                    $totalPrereleasesRemoved++;
                }
            }
        }

        // Log the total number of prereleases found, saved, and removed
        Util::logDebug("createQuickPickLive completed: Found $totalPrereleasesFound prerelease(s), saved $totalPrereleasesSaved prerelease version(s), removed $totalPrereleasesRemoved version(s) from quickpick-releases.json");

        // Always add a timestamp to the file to track when it was last updated by the PR method
        // This ensures the cache time is properly tracked even if no new versions were saved
        $this->addPrUpdateTimestamp();
        if ($totalPrereleasesSaved == 0 && $totalPrereleasesRemoved == 0) {
            Util::logDebug("No prerelease versions were saved or removed, but timestamp was updated in quickpick-releases.json");
        }

        // Return to normal QuickPick method(s)
        return $totalPrereleasesSaved;
    }

    /**
     * Adds a timestamp to the quickpick-releases.json file to track when it was last updated by the PR method.
     * 
     * @return bool True if the timestamp was added successfully, false otherwise.
     */
    private function addPrUpdateTimestamp(): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);
            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());
            return false;
        }

        // Add or update the timestamp
        $timestamp = date('Y-m-d H:i:s');
        $data['pr_last_update'] = $timestamp;

        // Save back to file (pretty print for readability)
        $result = file_put_contents($this->jsonFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($result === false) {
            Util::logError('Failed to write updated JSON with timestamp to file');
            return false;
        }

        Util::logDebug("Added PR update timestamp to quickpick-releases.json: $timestamp");
        return true;
    }

    /**
     * Restores a specific timestamp to the quickpick-releases.json file.
     * This is used to preserve the timestamp when the file is updated by other methods.
     * 
     * @param string $timestamp The timestamp to restore
     * @return bool True if the timestamp was restored successfully, false otherwise
     */
    private function restorePrUpdateTimestamp(string $timestamp): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);
            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());
            return false;
        }

        // Restore the timestamp
        $data['pr_last_update'] = $timestamp;

        // Save back to file (pretty print for readability)
        $result = file_put_contents($this->jsonFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($result === false) {
            Util::logError('Failed to write updated JSON with restored timestamp to file');
            return false;
        }

        Util::logDebug("Restored PR update timestamp to quickpick-releases.json: $timestamp");
        return true;
    }

    /**
     * Retrieves the list of available modules.
     *
     * @return array An array of module names.
     */
    public function getModules(): array
    {
        return array_keys($this->modules);
    }

    /**
     * Update quickpick-releases.json with a new version and URL for a module.
     *
     * This method adds a new version and URL to the quickpick-releases.json file for a specified module.
     * If the module doesn't exist in the file, it creates a new entry for it.
     * If the version already exists for the module, it doesn't add a duplicate.
     *
     * @param   string  $moduleName  The name of the module (e.g., 'python').
     * @param   string  $version     The version string (e.g., '3.10.9').
     * @param   string  $url         The download URL for the module version.
     * @param   bool    $prerelease  Whether this version is a prerelease.
     *
     * @return bool True if the update was successful, false if the version already exists or there was an error.
     */
    public function updateQuickpickReleasesJson(string $moduleName, string $version, string $url, bool $prerelease = false): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);

            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');

            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());

            return false;
        }

        $moduleKey = "module-" . strtolower($moduleName);
        $found     = false;
        $updated   = false;

        // Find the module entry
        foreach ($data as &$moduleEntry) {
            if (isset($moduleEntry['module']) && $moduleEntry['module'] === $moduleKey) {
                $found = true;
                // Check for duplicate version
                foreach ($moduleEntry['versions'] as &$entry) {
                    if (isset($entry['version']) && $entry['version'] === $version) {
                        // Version already exists, check if we need to update the prerelease flag
                        if ($prerelease && (!isset($entry['prerelease']) || $entry['prerelease'] !== true)) {
                            Util::logDebug("Updating prerelease flag for version $version of module $moduleName");
                            $entry['prerelease'] = true;
                            $updated = true;
                        } else {
                            Util::logDebug("Version $version already exists for module $moduleName");
                            return $updated; // Return true if we updated the prerelease flag, false otherwise
                        }
                        break;
                    }
                }

                if (!$updated) {
                    // Add new version entry if we didn't update an existing one
                    $moduleEntry['versions'][] = [
                        'version'    => $version,
                        'url'        => $url,
                        'prerelease' => $prerelease
                    ];
                    $updated = true;

                    // Sort versions in ascending semver order
                    usort($moduleEntry['versions'], function($a, $b) {
                        return version_compare($a['version'], $b['version']);
                    });
                }
                break;
            }
        }
        unset($moduleEntry);

        // If module not found, add new module entry
        if (!$found) {
            $data[] = [
                'module'   => $moduleKey,
                'versions' => [
                    [
                        'version'    => $version,
                        'url'        => $url,
                        'prerelease' => $prerelease
                    ]
                ]
            ];
            $updated = true;
        }

        // Only save if we actually made changes
        if ($updated) {
            // Preserve the PR update timestamp if it exists
            $prLastUpdate = null;
            if (isset($data['pr_last_update'])) {
                $prLastUpdate = $data['pr_last_update'];
            }

            // Save back to file (pretty print for readability)
            $result = file_put_contents($this->jsonFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            if ($result === false) {
                Util::logError('Failed to write updated JSON to file');
                return false;
            }

            // Restore the PR update timestamp if it was present
            if ($prLastUpdate !== null) {
                $this->restorePrUpdateTimestamp($prLastUpdate);
            }

            Util::logDebug("Successfully added/updated version $version for module $moduleName");
        }

        return $updated;
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
        try {
            $this->checkQuickpickJson();

            $modules  = $this->getModules();
            $versions = $this->getVersions();

            return $this->getQuickpickMenu($modules, $versions, $imagesPath);
        } catch (Exception $e) {
            Util::logError('Error in loadQuickpick: ' . $e->getMessage());
            // Return a minimal HTML structure to prevent the page from breaking
            return '<div id="quickPickContainer" class="text-center mt-3 pe-3">
                <span>QuickPick unavailable</span>
            </div>';
        }
    }

    /**
     * Checks if the local quickpick-releases.json file is up-to-date with the remote version.
     * If the remote file is newer or the local file does not exist, it fetches the latest JSON data by calling
     * the rebuildQuickpickJson method. If IncludePR is enabled, merges PRs into the local file.
     *
     * @return bool Returns true if the file was updated, false if no update was needed.
     * @throws Exception
     */
    public function checkQuickpickJson()
    {
        // First, check if the local file exists and get its modification time
        if (!file_exists($this->jsonFilePath)) {
            // If local file is missing, rebuild it immediately
            Util::logDebug('Local file does not exist, rebuilding quickpick-releases.json');
            $this->rebuildQuickpickJson();

            // If IncludePR is enabled, load prerelease versions
            if ($this->isIncludePrEnabled()) {
                Util::logDebug('IncludePR is enabled, loading prerelease versions for new file');
                // First check if there are any prereleases to load
                $result = $this->createQuickPickLive();
                if ($result === 0) {
                    Util::logDebug('No prerelease versions found, using remote version as-is');
                    // If no prereleases were found, rebuild the file from remote to ensure it's identical to the remote version
                    $this->rebuildQuickpickJson();
                }
            }
            return true;
        }

        // Get the local file's modification time
        $localFileModTime = $this->getLocalFileCreationTime();

        // Check if we need to update the PR data based on cache time
        if ($this->isIncludePrEnabled()) {
            // Check if the PR cache has expired
            if (!$this->isPrCacheValid()) {
                Util::logDebug('PR cache has expired, reloading prerelease versions');
                // First check if there are any prereleases to load
                $result = $this->createQuickPickLive();
                if ($result === 0) {
                    Util::logDebug('No prerelease versions found, using remote version as-is');
                    // If no prereleases were found, rebuild the file from remote to ensure it's identical to the remote version
                    $this->rebuildQuickpickJson();
                }
                return true;
            }

            // Check if the file contains prerelease versions
            if (!$this->hasPrereleaseVersions()) {
                Util::logDebug('File does not contain prerelease versions, reloading PRs');
                // First check if there are any prereleases to load
                $result = $this->createQuickPickLive();
                if ($result === 0) {
                    Util::logDebug('No prerelease versions found, using remote version as-is');
                    // If no prereleases were found, rebuild the file from remote to ensure it's identical to the remote version
                    $this->rebuildQuickpickJson();
                }
                return true;
            }
        }

        // Now check if we need to update the base file from remote
        // Attempt to retrieve remote file headers
        $headers = get_headers(QUICKPICK_JSON_URL, 1);
        if (!$this->isValidHeaderResponse($headers)) {
            // If headers or Date are invalid, assume no update needed
            Util::logDebug('Could not retrieve valid headers from remote file, using existing file');
            return false;
        }

        // Optionally log headers for verbose output
        $this->logHeaders($headers);

        // Get the Last-Modified header if available, otherwise use Date
        $remoteLastModified = isset($headers['Last-Modified']) ? $headers['Last-Modified'] : (isset($headers['Date']) ? $headers['Date'] : '');
        $remoteFileModTime = strtotime($remoteLastModified);

        // Compare the modification times
        if ($remoteFileModTime > $localFileModTime) {
            // Remote file is newer, update local file
            Util::logDebug('Remote file is newer, rebuilding quickpick-releases.json');
            $this->rebuildQuickpickJson();

            // If IncludePR is enabled and cache has expired, reload PRs
            if ($this->isIncludePrEnabled() && !$this->isPrCacheValid()) {
                Util::logDebug('After rebuild: PR cache has expired, reloading prerelease versions');
                // First check if there are any prereleases to load
                $result = $this->createQuickPickLive();
                if ($result === 0) {
                    Util::logDebug('After rebuild: No prerelease versions found, using remote version as-is');
                    // If no prereleases were found, rebuild the file from remote to ensure it's identical to the remote version
                    $this->rebuildQuickpickJson();
                }
            } else if ($this->isIncludePrEnabled()) {
                // Verify if the file contains prerelease versions after rebuild
                Util::logDebug('After rebuild: Checking if file contains prerelease versions');
                if (!$this->hasPrereleaseVersions()) {
                    Util::logDebug('After rebuild: File does not contain prerelease versions, reloading PRs');
                    // First check if there are any prereleases to load
                    $result = $this->createQuickPickLive();
                    if ($result === 0) {
                        Util::logDebug('After rebuild: No prerelease versions found, using remote version as-is');
                        // If no prereleases were found, rebuild the file from remote to ensure it's identical to the remote version
                        $this->rebuildQuickpickJson();
                    }
                } else {
                    Util::logDebug('After rebuild: File contains prerelease versions, no need to reload PRs');
                }
            }
            return true;
        }

        // If we got here, the local file is up-to-date
        Util::logDebug('Local file is up-to-date, no changes needed');
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

        // Use filemtime instead of filectime to get the last modification time
        // This is more reliable for comparing with the remote file's timestamp
        return filemtime($this->jsonFilePath);
    }

    /**
     * Rebuilds the local quickpick-releases.json file by fetching the latest data from the remote URL.
     *
     * @return array An array containing the status and message of the rebuild process.
     * @throws Exception If the JSON content cannot be fetched or saved.
     */
    public function rebuildQuickpickJson(): array
    {
        Util::logDebug('Fetching JSON file: ' . $this->jsonFilePath);

        // Fetch the JSON content from the URL
        $jsonContent = file_get_contents(QUICKPICK_JSON_URL);

        if ($jsonContent === false) {
            // Handle error if the file could not be fetched
            throw new Exception('Failed to fetch JSON content from the URL.');
        }

        // Always try to preserve the PR update timestamp
        $prLastUpdate = null;
        $existingPrereleases = [];

        // Check if we need to preserve prerelease versions and/or timestamp
        $preserveData = file_exists($this->jsonFilePath);
        $hasPrereleasesInFile = false;

        if ($preserveData) {
            $existingJson = file_get_contents($this->jsonFilePath);
            if ($existingJson !== false) {
                $existingData = json_decode($existingJson, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Always preserve the PR update timestamp if it exists
                    if (isset($existingData['pr_last_update'])) {
                        $prLastUpdate = $existingData['pr_last_update'];
                        Util::logTrace('Preserving PR update timestamp: ' . $prLastUpdate);
                    }

                    // Check if there are any prerelease versions in the existing file
                    foreach ($existingData as $entry) {
                        if (is_array($entry) && isset($entry['module']) && isset($entry['versions']) && is_array($entry['versions'])) {
                            foreach ($entry['versions'] as $version) {
                                if (isset($version['prerelease']) && $version['prerelease'] === true) {
                                    $hasPrereleasesInFile = true;
                                    break 2; // Break out of both loops once we find a prerelease
                                }
                            }
                        }
                    }

                    // Only preserve prereleases if IncludePR is enabled AND there are actual prereleases in the file
                    $preservePrereleases = $this->isIncludePrEnabled() && $hasPrereleasesInFile;

                    // Extract prerelease versions from existing file if needed
                    if ($preservePrereleases) {
                        Util::logDebug('Preserving prerelease versions when rebuilding quickpick-releases.json');
                        foreach ($existingData as $entry) {
                            if (is_array($entry) && isset($entry['module']) && isset($entry['versions']) && is_array($entry['versions'])) {
                                $moduleKey = $entry['module'];
                                $existingPrereleases[$moduleKey] = [];

                                foreach ($entry['versions'] as $version) {
                                    if (isset($version['prerelease']) && $version['prerelease'] === true) {
                                        $existingPrereleases[$moduleKey][] = $version;
                                    }
                                }
                            }
                        }
                    } else if (!$hasPrereleasesInFile) {
                        Util::logDebug('No prerelease versions found in existing file, using remote version as-is');
                    }
                }
            }
        }

        // Parse the new JSON content
        $newData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode JSON content from the URL: ' . json_last_error_msg());
        }

        // Merge prerelease versions back into the new data if needed
        if ($preservePrereleases && !empty($existingPrereleases) && $hasPrereleasesInFile) {
            Util::logDebug('Merging prerelease versions back into the new data');
            foreach ($newData as &$entry) {
                if (is_array($entry) && isset($entry['module']) && isset($entry['versions']) && is_array($entry['versions'])) {
                    $moduleKey = $entry['module'];

                    // If we have prerelease versions for this module, merge them
                    if (isset($existingPrereleases[$moduleKey]) && !empty($existingPrereleases[$moduleKey])) {
                        $prereleaseVersions = $existingPrereleases[$moduleKey];

                        // Add prerelease versions that don't already exist in the new data
                        foreach ($prereleaseVersions as $prereleaseVersion) {
                            $versionExists = false;
                            foreach ($entry['versions'] as $version) {
                                if ($version['version'] === $prereleaseVersion['version']) {
                                    $versionExists = true;
                                    break;
                                }
                            }

                            if (!$versionExists) {
                                $entry['versions'][] = $prereleaseVersion;
                            }
                        }

                        // Sort versions in ascending semver order
                        usort($entry['versions'], function($a, $b) {
                            return version_compare($a['version'], $b['version']);
                        });
                    }
                }
            }
        } else if ($preservePrereleases && !$hasPrereleasesInFile) {
            Util::logDebug('No prerelease versions to merge, using remote version as-is');
        }

        // Only restore the PR update timestamp if it exists AND we're preserving prereleases AND there are actual prereleases
        if ($prLastUpdate !== null && $preservePrereleases && $hasPrereleasesInFile) {
            $newData['pr_last_update'] = $prLastUpdate;
            Util::logDebug('Restored PR update timestamp in rebuilt JSON: ' . $prLastUpdate);
        } else if ($prLastUpdate !== null && (!$preservePrereleases || !$hasPrereleasesInFile)) {
            Util::logDebug('Not restoring PR update timestamp because there are no prereleases to preserve or no prereleases in file');
        }

        // Save the updated JSON content to the specified path
        $result = file_put_contents($this->jsonFilePath, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if ($result === false) {
            // Handle error if the file could not be saved
            throw new Exception('Failed to save JSON content to the specified path.');
        }

        // Return success message
        return ['success' => 'JSON content fetched and saved successfully'];
    }

    /**
     * Helper to check if IncludePR is enabled in the config.
     *
     * @return bool
     */
    private function isIncludePrEnabled(): bool
    {
        global $bearsamppConfig;
        return (bool)$bearsamppConfig->getIncludePr();
    }

    /**
     * Checks if it's been less than IncludePRCacheTime minutes since the last PR reload.
     * Uses the 'pr_last_update' timestamp in the JSON file if available, otherwise falls back to file modification time.
     * 
     * @return bool True if cache is still valid, false if cache has expired
     */
    private function isPrCacheValid(): bool
    {
        global $bearsamppConfig;

        // Get the cache time in minutes from config
        $cacheTimeMinutes = $bearsamppConfig->getIncludePrCacheTime();

        // If cache time is 0, always reload
        if ($cacheTimeMinutes <= 0) {
            Util::logDebug('Cache time is 0 or less, always reloading PRs');
            return false;
        }

        // Check if PR cache file exists
        $prCacheFile = $this->jsonFilePath;
        if (!file_exists($prCacheFile)) {
            Util::logDebug('PR cache file does not exist');
            return false;
        }

        // Try to get the PR update timestamp from the file
        $lastUpdateTime = $this->getPrLastUpdateTime();

        if ($lastUpdateTime === 0) {
            // If no PR update timestamp found, fall back to file modification time
            Util::logDebug('No PR update timestamp found, using file modification time');
            $lastUpdateTime = filemtime($prCacheFile);
        } else {
            Util::logDebug('Using PR update timestamp: ' . date('Y-m-d H:i:s', $lastUpdateTime));
        }

        // Calculate time difference in minutes
        $timeDiff = (time() - $lastUpdateTime) / 60;
        Util::logDebug("Time since last PR update: $timeDiff minutes (cache time: $cacheTimeMinutes minutes)");

        // Return true if cache is still valid (less than cache time)
        return $timeDiff < $cacheTimeMinutes;
    }

    /**
     * Gets the timestamp of the last PR update from the quickpick-releases.json file.
     * 
     * @return int The timestamp of the last PR update, or 0 if not found
     */
    private function getPrLastUpdateTime(): int
    {
        if (!file_exists($this->jsonFilePath)) {
            return 0;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            return 0;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return 0;
        }

        if (isset($data['pr_last_update'])) {
            // Convert the timestamp string to a Unix timestamp
            $timestamp = strtotime($data['pr_last_update']);
            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return 0;
    }

    /**
     * Removes the prerelease flag from a specific version of a module in the quickpick-releases.json file.
     * 
     * @param string $moduleName The name of the module
     * @param string $version The version to remove the prerelease flag from
     * @return bool True if the prerelease flag was removed, false otherwise
     */
    private function removePrereleaseFlagFromVersion(string $moduleName, string $version): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);
            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());
            return false;
        }

        $moduleKey = "module-" . strtolower($moduleName);
        $updated = false;

        // Find the module entry
        foreach ($data as &$moduleEntry) {
            if (isset($moduleEntry['module']) && $moduleEntry['module'] === $moduleKey) {
                // Find the version entry
                foreach ($moduleEntry['versions'] as &$versionEntry) {
                    if (isset($versionEntry['version']) && $versionEntry['version'] === $version) {
                        // Remove the prerelease flag
                        if (isset($versionEntry['prerelease'])) {
                            unset($versionEntry['prerelease']);
                            $updated = true;
                            Util::logDebug("Removed prerelease flag from version $version of module $moduleName");
                        }
                        break;
                    }
                }
                break;
            }
        }
        unset($moduleEntry, $versionEntry);

        if ($updated) {
            // Preserve the PR update timestamp if it exists
            $prLastUpdate = null;
            if (isset($data['pr_last_update'])) {
                $prLastUpdate = $data['pr_last_update'];
            }

            // Save back to file (pretty print for readability)
            $result = file_put_contents($this->jsonFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            if ($result === false) {
                Util::logError('Failed to write updated JSON to file');
                return false;
            }

            // Restore the PR update timestamp if it was present
            if ($prLastUpdate !== null) {
                $this->restorePrUpdateTimestamp($prLastUpdate);
            }
        }

        return $updated;
    }

    /**
     * Completely removes a specific version of a module from the quickpick-releases.json file.
     * This is used when a version is no longer a prerelease and should be removed from the file.
     * 
     * @param string $moduleName The name of the module
     * @param string $version The version to remove
     * @return bool True if the version was removed, false otherwise
     */
    private function removeVersionFromJson(string $moduleName, string $version): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);
            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());
            return false;
        }

        $moduleKey = "module-" . strtolower($moduleName);
        $updated = false;

        // Find the module entry
        foreach ($data as &$moduleEntry) {
            if (isset($moduleEntry['module']) && $moduleEntry['module'] === $moduleKey) {
                // Find and remove the version entry
                if (isset($moduleEntry['versions']) && is_array($moduleEntry['versions'])) {
                    $versionIndex = -1;
                    foreach ($moduleEntry['versions'] as $index => $versionEntry) {
                        if (isset($versionEntry['version']) && $versionEntry['version'] === $version) {
                            $versionIndex = $index;
                            break;
                        }
                    }

                    if ($versionIndex >= 0) {
                        // Remove the version from the array
                        array_splice($moduleEntry['versions'], $versionIndex, 1);
                        $updated = true;
                        Util::logDebug("Removed version $version of module $moduleName from quickpick-releases.json");
                    }
                }
                break;
            }
        }
        unset($moduleEntry);

        if ($updated) {
            // Preserve the PR update timestamp if it exists
            $prLastUpdate = null;
            if (isset($data['pr_last_update'])) {
                $prLastUpdate = $data['pr_last_update'];
            }

            // Save back to file (pretty print for readability)
            $result = file_put_contents($this->jsonFilePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            if ($result === false) {
                Util::logError('Failed to write updated JSON to file');
                return false;
            }

            // Restore the PR update timestamp if it was present
            if ($prLastUpdate !== null) {
                $this->restorePrUpdateTimestamp($prLastUpdate);
            }
        }

        return $updated;
    }

    /**
     * Checks if the quickpick-releases.json file contains any prerelease versions.
     * This can be used to verify that the PR method is working as expected.
     * 
     * @return bool True if the file contains prerelease versions, false otherwise.
     */
    public function hasPrereleaseVersions(): bool
    {
        if (!file_exists($this->jsonFilePath)) {
            Util::logError('quickpick-releases.json file not found at: ' . $this->jsonFilePath);
            return false;
        }

        $json = file_get_contents($this->jsonFilePath);
        if ($json === false) {
            Util::logError('Failed to read quickpick-releases.json file');
            return false;
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Failed to decode JSON: ' . json_last_error_msg());
            return false;
        }

        // Check if the file has a PR update timestamp
        if (isset($data['pr_last_update'])) {
            Util::logDebug('Found PR update timestamp in quickpick-releases.json: ' . $data['pr_last_update']);
        }

        // Check each module for prerelease versions
        $prereleaseCount = 0;
        foreach ($data as $entry) {
            if (is_array($entry) && isset($entry['module']) && isset($entry['versions']) && is_array($entry['versions'])) {
                foreach ($entry['versions'] as $version) {
                    if (isset($version['prerelease']) && $version['prerelease'] === true) {
                        $prereleaseCount++;
                        Util::logTrace('Found prerelease: ' . json_encode($version));
                    }
                }
            }
        }

        Util::logTrace("Found $prereleaseCount prerelease versions in quickpick-releases.json");
        return $prereleaseCount > 0;
    }

    /**
     * Determines whether the header response is valid and includes a 'Date' key.
     *
     * @param   mixed  $headers  Headers retrieved from get_headers().
     *
     * @return bool True if headers are valid and contain 'Date', false otherwise.
     */
    private function isValidHeaderResponse($headers): bool
    {
        // If headers retrieval failed, return false
        if ($headers === false) {
            return false;
        }

        // Check if either Date or Last-Modified headers are available
        if (!isset($headers['Date']) && !isset($headers['Last-Modified'])) {
            Util::logDebug('Neither Date nor Last-Modified headers are available in the response');
            return false;
        }

        return true;
    }

    /**
     * Logs the headers in debug mode if logsVerbose is set to 2.
     *
     * @param   array  $headers  The headers returned by get_headers().
     */
    private function logHeaders(array $headers): void
    {
        global $bearsamppConfig;

        if ($bearsamppConfig->getLogsVerbose() === 2) {
            Util::logDebug('Headers: ' . print_r($headers, true));
        }
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
        Util::logDebug('Versions called');

        $versions = [];

        $jsonData = $this->getQuickpickJson();

        // First, ensure all modules have an entry in the versions array
        $modules = $this->getModules();
        foreach ($modules as $module) {
            $moduleKey = 'module-' . strtolower($module);
            $versions[$moduleKey] = [];
        }

        // Then populate with actual versions from JSON data
        foreach ($jsonData as $entry) {
            if (is_array($entry)) {
                if (isset($entry['module']) && is_string($entry['module'])) {
                    if (isset($entry['versions']) && is_array($entry['versions'])) {
                        $versions[$entry['module']] = array_column($entry['versions'], null, 'version');
                    }
                }
            } else {
                Util::logError('Invalid entry format in JSON data');
            }
        }

        if (empty($jsonData)) {
            Util::logError('No versions found in JSON data');
        }

        Util::logTrace('Found versions');

        $this->versions = $versions;

        return $versions;
    }

    /**
     * Retrieves the QuickPick JSON data from the local file.
     *
     * @return array The decoded JSON data, or an error message if the file cannot be fetched or decoded.
     */
    public function getQuickpickJson(): array
    {
        $content = @file_get_contents($this->jsonFilePath);
        if ($content === false) {
            Util::logError('Error fetching content from JSON file: ' . $this->jsonFilePath);

            return ['error' => 'Error fetching JSON file'];
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Error decoding JSON content: ' . json_last_error_msg());

            return ['error' => 'Error decoding JSON content'];
        }

        return $data;
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
        ob_start();
        if (Util::checkInternetState()) {
            // Check if the license key is valid
            if ($this->checkDownloadId()): ?>
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
                                foreach ($modules as $module): ?>
                                    <?php
                                    if (is_string($module)): ?>
										<li role = "option" class = "moduleheader">
                                            <?php
                                            echo htmlspecialchars($module); ?>
										</li>

                                        <?php
                                        foreach ($versions['module-' . strtolower($module)] as $version_array):
                                            $isPrerelease = !empty($version_array['prerelease']);
                                            $labelClass = $isPrerelease ? 'text-danger' : '';
                                            $labelText = htmlspecialchars($version_array['version']) . ($isPrerelease ? ' PR' : '');
                                            ?>
											<li role = "option" class = "moduleoption"
											    id = "<?php
                                                echo htmlspecialchars($module); ?>-version-<?php
                                                echo htmlspecialchars($version_array['version']); ?>-li">
												<input type = "radio"
												       id = "<?php
                                                       echo htmlspecialchars($module); ?>-version-<?php
                                                       echo htmlspecialchars($version_array['version']); ?>"
												       name = "module" data-module = "<?php
                                                echo htmlspecialchars($module); ?>"
												       data-value = "<?php
                                                       echo htmlspecialchars($version_array['version']); ?>">
												<label
														for = "<?php
                                                        echo htmlspecialchars($module); ?>-version-<?php
                                                        echo htmlspecialchars($version_array['version']); ?>"
                                                    <?php
                                                    if ($labelClass) {
                                                        echo 'class="' . $labelClass . '"';
                                                    } ?>>
                                                    <?php
                                                    echo $labelText; ?>
												</label>
											</li>
                                        <?php
                                        endforeach; ?>
                                    <?php
                                    endif; ?>
                                <?php
                                endforeach; ?>
							</ul>
						</div>
					</div>
					<div class = "progress " id = "progress" tabindex = "-1" style = "width:260px;display:none"
					     aria-labelledby = "progress-bar" aria-hidden = "true">
						<div class = "progress-bar progress-bar-striped progress-bar-animated" id = "progress-bar" role = "progressbar" aria-valuenow = "0" aria-valuemin = "0"
						     aria-valuemax = "100" data-module = "Module"
						     data-version = "0.0.0">0%
						</div>
						<div id = "download-module" style = "display: none">ModuleName</div>
						<div id = "download-version" style = "display: none">Version</div>
					</div>
				</div>
            <?php
            else: ?>
				<div id = "subscribeContainer" class = "text-center mt-3 pe-3">
					<a href = "<?php
                    echo Util::getWebsiteUrl('subscribe'); ?>" class = "btn btn-dark d-inline-flex align-items-center">
						<img src = "<?php
                        echo $imagesPath . 'subscribe.svg'; ?>" alt = "Subscribe Icon" class = "me-2">
						Subscribe to QuickPick now
					</a>
				</div>
            <?php
            endif;
        } else {
            ?>
			<div id = "InternetState" class = "text-center mt-3 pe-3">
				<img src = "<?php
                echo $imagesPath . 'no-wifi-icon.svg'; ?>" alt = "No Wifi Icon" class = "me-2">
				<span>No internet present</span>
			</div>
            <?php
        }

        return ob_get_clean();
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

        Util::logDebug('checkDownloadId method called.');

        // Ensure the global config is available
        if (!isset($bearsamppConfig)) {
            Util::logError('Global configuration is not set.');

            return false;
        }

        $DownloadId = $bearsamppConfig->getDownloadId();
        Util::logDebug('DownloadId is: ' . $DownloadId);

        // Ensure the license key is not empty
        if (empty($DownloadId)) {
            Util::logError('License key is empty.');

            return false;
        }

        $url = QUICKPICK_API_URL . QUICKPICK_API_KEY . '&download_id=' . $DownloadId;
        Util::logDebug('API URL: ' . $url);

        $response = @file_get_contents($url);

        // Check if the response is false
        if ($response === false) {
            $error = error_get_last();
            Util::logError('Error fetching API response: ' . $error['message']);

            return false;
        }

        Util::logDebug('API response: ' . $response);

        $data = json_decode($response, true);

        // Check if the JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            Util::logError('Error decoding JSON response: ' . json_last_error_msg());

            return false;
        }

        // Validate the response data
        if (isset($data['success']) && $data['success'] === true && isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
            Util::logDebug('License key valid: ' . $DownloadId);

            return true;
        }

        Util::logError('Invalid license key: ' . $DownloadId);

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
        Util::logTrace('Installing module: ' . $module . ' version: ' . $version);
        $moduleUrl = $this->getModuleUrl($module, $version);

        if (is_array($moduleUrl) && isset($moduleUrl['error'])) {
            Util::logError('Module URL not found for module: ' . $module . ' version: ' . $version);

            return ['error' => 'Module URL not found'];
        }

        if (empty($moduleUrl)) {
            Util::logError('Module URL not found for module: ' . $module . ' version: ' . $version);

            return ['error' => 'Module URL not found'];
        }

        $internet = Util::checkInternetState();
        if ($internet) {
            $response = $this->fetchAndUnzipModule($moduleUrl, $module);
            Util::logTrace('Response is: ' . print_r($response, true));

            return $response;
        } else {
            Util::logError('No internet connection available.');

            return ['error' => 'No internet connection'];
        }
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
        Util::logTrace('getModuleUrl called for module: ' . $module . ' version: ' . $version);
        $url = trim($this->versions['module-' . strtolower($module)][$version]['url']);
        if ($url <> '') {
            Util::logDebug('Found URL for version: ' . $version . ' URL: ' . $url);

            return $url;
        } else {
            Util::logError('Version not found: ' . $version);

            return ['error' => 'Version not found'];
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
        if ($moduleType === 'application') {
            $destination = $bearsamppRoot->getAppsPath() . '/' . strtolower($moduleName) . '/';
        } elseif ($moduleType === 'binary') {
            $destination = $bearsamppRoot->getBinPath() . '/' . strtolower($moduleName) . '/';
        } elseif ($moduleType === 'tools') {
            $destination = $bearsamppRoot->getToolsPath() . '/' . strtolower($moduleName) . '/';
        } else {
            $destination = '';
        }

        return $destination;
    }
}
