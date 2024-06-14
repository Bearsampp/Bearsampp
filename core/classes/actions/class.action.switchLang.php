<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionSwitchLang
 *
 * This class handles the action of switching the language configuration for the Bearsampp application.
 */
class ActionSwitchLang
{
    /**
     * ActionSwitchLang constructor.
     *
     * @param array $args An array of arguments where the first element is expected to be the new language code.
     */
    public function __construct($args)
    {
        global $bearsamppConfig;

        // Check if the first argument is set and not empty, then replace the language configuration.
        if (isset($args[0]) && !empty($args[0])) {
            $bearsamppConfig->replace(Config::CFG_LANG, $args[0]);
        }
    }
}
