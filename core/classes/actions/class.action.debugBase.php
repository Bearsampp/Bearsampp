<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ActionDebugBase
 *
 * Base class for debugging actions across different services (MySQL, MariaDB, Apache, PostgreSQL).
 * This class provides common functionality for executing debug commands and displaying their output.
 * Child classes need to implement service-specific methods to define their behavior.
 */
abstract class ActionDebugBase
{
    /**
     * Get the service name for language strings (e.g., 'MYSQL', 'APACHE', 'MARIADB', 'POSTGRESQL')
     *
     * @return string The language constant name for the service
     */
    abstract protected function getServiceLangConstant();

    /**
     * Get the binary instance for this service
     *
     * @param object $bearsamppBins The bins object containing all service binaries
     * @return object The specific binary instance (e.g., BinMysql, BinApache)
     */
    abstract protected function getBinInstance($bearsamppBins);

    /**
     * Get the command-to-caption mapping for this service
     * Returns an array where keys are command constants and values are arrays with:
     * - 'lang': the language constant for the caption
     * - 'editor': boolean indicating if output should be shown in editor (default: false)
     *
     * @return array Command mapping configuration
     */
    abstract protected function getCommandMapping();

    /**
     * Check if the debug output has a 'content' key (for services that return arrays)
     * or if it's a direct string (for services like PostgreSQL)
     *
     * @return bool True if output is an array with 'content' key, false if direct string
     */
    protected function hasContentKey()
    {
        return true;
    }

    /**
     * Constructor for debug actions.
     *
     * @param array $args An array of arguments where the first element is the command to execute.
     *
     * This constructor handles the common debugging workflow:
     * 1. Validates arguments
     * 2. Builds the caption based on the command
     * 3. Executes the command and retrieves output
     * 4. Handles syntax check results if applicable
     * 5. Displays output in editor or message box
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppTools, $bearsamppWinbinder;

        if (isset($args[0]) && !empty($args[0])) {
            $editor = false;
            $msgBoxError = false;

            // Build base caption
            $serviceLangConstant = $this->getServiceLangConstant();
            $caption = $bearsamppLang->getValue(Lang::DEBUG) . ' ' .
                       $bearsamppLang->getValue(constant('Lang::' . $serviceLangConstant)) . ' - ';

            // Get command mapping and determine caption suffix and editor flag
            $commandMapping = $this->getCommandMapping();
            $command = $args[0];

            if (isset($commandMapping[$command])) {
                $config = $commandMapping[$command];
                $caption .= $bearsamppLang->getValue($config['lang']);
                if (isset($config['editor']) && $config['editor']) {
                    $editor = true;
                }
            }

            $caption .= ' (' . $command . ')';

            // Execute the command and get output
            $bin = $this->getBinInstance($bearsamppBins);
            $debugOutput = $bin->getCmdLineOutput($command);

            // Handle syntax check results (if applicable)
            if ($this->isSyntaxCheckCommand($command)) {
                if ($this->hasContentKey()) {
                    $msgBoxError = !$debugOutput['syntaxOk'];
                    $debugOutput['content'] = $debugOutput['syntaxOk'] ? 'Syntax OK !' : $debugOutput['content'];
                }
            }

            // Extract content based on service type
            $content = $this->hasContentKey() && is_array($debugOutput) ? $debugOutput['content'] : $debugOutput;

            // Display the output
            if ($editor) {
                Util::openFileContent($caption, $content);
            } else {
                if ($msgBoxError) {
                    $bearsamppWinbinder->messageBoxError($content, $caption);
                } else {
                    $bearsamppWinbinder->messageBoxInfo($content, $caption);
                }
            }
        }
    }

    /**
     * Check if the given command is a syntax check command
     *
     * @param string $command The command to check
     * @return bool True if it's a syntax check command
     */
    protected function isSyntaxCheckCommand($command)
    {
        $commandMapping = $this->getCommandMapping();
        if (isset($commandMapping[$command])) {
            return isset($commandMapping[$command]['syntaxCheck']) && $commandMapping[$command]['syntaxCheck'];
        }
        return false;
    }
}
