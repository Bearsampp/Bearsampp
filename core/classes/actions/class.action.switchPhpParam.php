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
 * Class ActionSwitchPhpParam
 *
 * This class handles the switching of PHP parameters on and off.
 * It modifies the PHP configuration file based on the provided arguments.
 */
class ActionSwitchPhpParam
{
    const SWITCH_ON = 'on';
    const SWITCH_OFF = 'off';

    /**
     * Constructor for ActionSwitchPhpParam.
     *
     * @param array $args An array containing the PHP setting name and the desired state ('on' or 'off').
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        // Check if the required arguments are provided and not empty
        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            // Check if the PHP setting exists
            if (!$bearsamppBins->getPhp()->isSettingExists($args[0])) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::SWITCH_PHP_SETTING_NOT_FOUND), $args[0], $bearsamppBins->getPhp()->getVersion()),
                    $bearsamppLang->getValue(Lang::SWITCH_PHP_SETTING_TITLE)
                );
                return;
            }

            // Retrieve the current settings values
            $settingsValues = $bearsamppBins->getPhp()->getSettingsValues();
            if (isset($settingsValues[$args[0]])) {
                $onContent = $args[0] . ' = ' . $settingsValues[$args[0]][0];
                $offContent = $args[0] . ' = ' . $settingsValues[$args[0]][1];

                // Read the current PHP configuration file content
                $phpiniContent = file_get_contents($bearsamppBins->getPhp()->getConf());
                if ($args[1] == self::SWITCH_ON) {
                    // Replace the off setting with the on setting
                    $phpiniContent = str_replace($offContent, $onContent, $phpiniContent);
                } elseif ($args[1] == self::SWITCH_OFF) {
                    // Replace the on setting with the off setting
                    $phpiniContent = str_replace($onContent, $offContent, $phpiniContent);
                }

                // Write the updated content back to the PHP configuration file
                file_put_contents($bearsamppBins->getPhp()->getConf(), $phpiniContent);
            }
        }
    }
}
