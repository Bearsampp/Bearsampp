<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchPhpExtension
 *
 * This class is responsible for enabling or disabling PHP extensions by modifying the php.ini configuration file.
 */
class ActionSwitchPhpExtension
{
    const SWITCH_ON = 'on';
    const SWITCH_OFF = 'off';

    /**
     * ActionSwitchPhpExtension constructor.
     *
     * @param   array  $args  An array containing the extension name and the action (on/off).
     */
    public function __construct($args)
    {
        global $bearsamppBins;

        if (isset($args[0]) && !empty($args[0]) && isset($args[1]) && !empty($args[1])) {
            $onContent  = 'extension=' . $args[0];
            $offContent = ';extension=' . $args[0];

            $phpiniContent = file_get_contents($bearsamppBins->getPhp()->getConf());
            if ($args[1] == self::SWITCH_ON) {
                $phpiniContent = str_replace($offContent, $onContent, $phpiniContent);
            } elseif ($args[1] == self::SWITCH_OFF) {
                $phpiniContent = str_replace($onContent, $offContent, $phpiniContent);
            }

            $phpiniContentOr = file_get_contents($bearsamppBins->getPhp()->getConf());
            if ($phpiniContent == $phpiniContentOr && file_exists($bearsamppBins->getPhp()->getSymlinkPath() . '/ext/php_' . $args[0] . '.dll')) {
                $extsIni       = $bearsamppBins->getPhp()->getExtensionsFromConf();
                $latestExt     = (end($extsIni) == '0' ? ';' : '');
                $latestExt     .= 'extension=' . key($extsIni);
                $phpiniContent = str_replace(
                    $latestExt,
                    $latestExt . PHP_EOL . $onContent,
                    $phpiniContent
                );
            }

            file_put_contents($bearsamppBins->getPhp()->getConf(), $phpiniContent);
        }
    }
}
