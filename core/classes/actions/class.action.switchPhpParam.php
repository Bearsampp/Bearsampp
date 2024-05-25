<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

class ActionSwitchPhpParam
{
    const SWITCH_ON = 'on';
    const SWITCH_OFF = 'off';

    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            if (!$bearsamppBins->getPhp()->isSettingExists($args[0])) {
                $bearsamppWinbinder->messageBoxError(
                    sprintf($bearsamppLang->getValue(Lang::SWITCH_PHP_SETTING_NOT_FOUND), $args[0], $bearsamppBins->getPhp()->getVersion()),
                    $bearsamppLang->getValue(Lang::SWITCH_PHP_SETTING_TITLE)
                );
                return;
            }

            $settingsValues = $bearsamppBins->getPhp()->getSettingsValues();
            if (isset($settingsValues[$args[0]])) {
                $onContent = $args[0] . ' = ' . $settingsValues[$args[0]][0];
                $offContent = $args[0] . ' = ' . $settingsValues[$args[0]][1];

                $phpiniContent = file_get_contents($bearsamppBins->getPhp()->getConf());
                if ($args[1] == self::SWITCH_ON) {
                    $phpiniContent = str_replace($offContent, $onContent, $phpiniContent);
                } elseif ($args[1] == self::SWITCH_OFF) {
                    $phpiniContent = str_replace($onContent, $offContent, $phpiniContent);
                }

                file_put_contents($bearsamppBins->getPhp()->getConf(), $phpiniContent);
            }
        }
    }
}
