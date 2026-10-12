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
 * Loading screen orchestration.
 *
 * Starts, stops, and updates the loading/progress screen shown during long tasks.
 */
class LoadingScreen
{
    /**
     * Initiates a loading process using external components.
     *
     * @return void
     */
    public static function startLoading()
    {
        global $bearsamppCore, $bearsamppWinbinder;

        Log::trace('startLoading() called');
        Log::trace('PHP executable: ' . Path::getPhpExe());
        Log::trace('Root file: ' . Core::isRoot_FILE);
        Log::trace('Action: ' . Action::LOADING);

        Log::trace('Executing command: ' . Path::getPhpExe() . ' ' . Core::isRoot_FILE . ' ' . Action::LOADING);

        $result = $bearsamppWinbinder->exec(Path::getPhpExe(), [Core::isRoot_FILE, Action::LOADING], true, false);
        Log::trace('exec() returned: ' . var_export($result, true));

        Log::trace('startLoading() completed');
    }

    /**
     * Stops a previously started loading process and cleans up related resources.
     *
     * @return void
     */
    public static function stopLoading()
    {
        global $bearsamppCore;
        if (file_exists($bearsamppCore->getLoadingPid())) {
            $pids = file($bearsamppCore->getLoadingPid());
            foreach ($pids as $pid) {
                Win32Ps::kill($pid);
            }
            @unlink($bearsamppCore->getLoadingPid());
        }

        // Clean up status file
        self::clearLoadingText();
    }

    /**
     * Updates the loading screen text (if loading screen is active)
     * This allows dynamic updates to show which service is being processed
     *
     * @param   string  $text  The text to display on the loading screen
     *
     * @return void
     */
    public static function updateLoadingText($text)
    {
        global $bearsamppCore;

        $statusFile = Path::getTmpPath() . '/loading_status.txt';
        file_put_contents($statusFile, json_encode(['text' => $text]));
    }

    /**
     * Clears the loading status file
     *
     * @return void
     */
    public static function clearLoadingText()
    {
        global $bearsamppCore;

        $statusFile = Path::getTmpPath() . '/loading_status.txt';
        if (file_exists($statusFile)) {
            @unlink($statusFile);
        }
    }
}
