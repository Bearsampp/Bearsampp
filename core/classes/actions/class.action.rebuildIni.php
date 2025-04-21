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
 * Handles the action of rebuilding the bearsampp.ini file within the application.
 *
 * This class is responsible for deleting the existing bearsampp.ini file and creating a new one
 * with the specified configuration content.
 */
class ActionRebuildini
{
    /**
     * Constructor for the ActionRebuildini class.
     *
     * Upon instantiation, it deletes the existing bearsampp.ini file and creates a new one with
     * the specified configuration content.
     *
     * @param array $args Arguments that might be used for further extension of constructor functionality.
     * @throws Exception If the bearsampp.ini file cannot be written.
     */
    public function __construct($args)
    {
        global $bearsamppRoot, $bearsamppCore;

        // Step 0: Delete the existing bearsampp.ini file
        $iniFilePath = $bearsamppRoot->getIniFilePath();

        if (file_exists($iniFilePath)) {
            unlink($iniFilePath);
        }

        // Process and update the bearsampp.ini file
        // Step 1: Prepare the configuration content
        $configContent = <<<EOD
[Config]
ImageList=sprites.dat
ServiceCheckInterval=1
TrayIconAllRunning=16
TrayIconSomeRunning=17
TrayIconNoneRunning=18
ID={Bearsampp}
AboutHeader=Bearsampp
AboutVersion=Version @RELEASE_VERSION@

[Services]
Name: bearsamppapache

[Messages]
AllRunningHint=All services running
SomeRunningHint=%n of %t services running
NoneRunningHint=None of %t services running

[StartupAction]
Action: run; FileName: "%AeTrayMenuPath%core/libs/php/php-win.exe"; Parameters: "root.php startup"; WorkingDir: "%AeTrayMenuPath%core"; Flags: waituntilterminated
Action: run; FileName: "%AeTrayMenuPath%core/libs/php/php-win.exe"; Parameters: "root.php reload"; WorkingDir: "%AeTrayMenuPath%core"; Flags: waituntilterminated
Action: resetservices
Action: readconfig
Action: run; FileName: "%AeTrayMenuPath%core/libs/php/php-win.exe"; Parameters: "root.php checkVersion"; WorkingDir: "%AeTrayMenuPath%core"; Flags: waituntilterminated
Action: run; FileName: "%AeTrayMenuPath%core/libs/php/php-win.exe"; Parameters: "root.php exec"; WorkingDir: "%AeTrayMenuPath%core"
EOD;

        // Step 2: Write to the file
        if (file_put_contents($iniFilePath, $configContent) === false) {
            throw new Exception("Failed to write to bearsampp.ini file.");
        }

        Util::logTrace('Calling triggerReload...');
        $reloadAction = TplAppReload::triggerReload($args);
        Util::logTrace('Reload action: ' . $reloadAction);
    }
}
