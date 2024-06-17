<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplAppPython
 *
 * This class provides methods to generate menu items and actions for managing Python tools
 * within the Bearsampp application. It includes functionalities for accessing Python console,
 * IDLE, and other Python-related executables.
 */
class TplAppPython
{
    // Constant for the Python menu identifier
    const MENU = 'python';

    /**
     * Generates the main Python menu with options to access Python tools.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     *
     * @return string The generated menu items and actions for Python.
     */
    public static function process()
    {
        global $bearsamppLang;

        return TplApp::getMenu($bearsamppLang->getValue(Lang::PYTHON), self::MENU, get_called_class());
    }

    /**
     * Generates the Python menu with options for accessing the Python console, IDLE, and other tools.
     *
     * @global object $bearsamppLang Provides language support for retrieving language-specific values.
     * @global object $bearsamppTools Provides access to various tools and their configurations.
     *
     * @return string The generated menu items and actions for Python tools.
     */
    public static function getMenuPython()
    {
        global $bearsamppLang, $bearsamppTools;

        // Generate menu item for Python console
        $resultItems = TplAestan::getItemConsoleZ(
            $bearsamppLang->getValue(Lang::PYTHON_CONSOLE),
            TplAestan::GLYPH_PYTHON,
            $bearsamppTools->getConsoleZ()->getTabTitlePython()
        ) . PHP_EOL;

        // Generate menu item for Python IDLE
        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::PYTHON) . ' IDLE',
            $bearsamppTools->getPython()->getIdleExe(),
            TplAestan::GLYPH_PYTHON
        ) . PHP_EOL;

        // Generate menu item for Python command prompt
        $resultItems .= TplAestan::getItemExe(
            $bearsamppLang->getValue(Lang::PYTHON_CP),
            $bearsamppTools->getPython()->getCpExe(),
            TplAestan::GLYPH_PYTHON_CP
        ) . PHP_EOL;

        return $resultItems;
    }
}
