<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class TplPowerShell
 *
 * This class is responsible for managing PowerShell configuration.
 * It includes methods to define various sections such as tabs for different tools.
 */
class TplPowerShell
{
    // Icon constants - Currently unused as native PowerShell consoles do not support custom icons
    // These icons were used with ConsoleZ but are not supported by standard PowerShell console host
    // To use icons, consider using a terminal emulator like ConEmu, Cmder, or Windows Terminal
    // Icon files are located at: core/resources/homepage/img/icons/
    /*
    const ICON_APP = 'app.ico';
    const ICON_POWERSHELL = 'powershell.ico';
    const ICON_PEAR = 'pear.ico';
    const ICON_DB = 'db.ico';
    const ICON_GHOSTSCRIPT = 'ghostscript.ico';
    const ICON_GIT = 'git.ico';
    const ICON_NODEJS = 'nodejs.ico';
    const ICON_COMPOSER = 'composer.ico';
    const ICON_PYTHON = 'python.ico';
    const ICON_RUBY = 'ruby.ico';
    const ICON_PERL = 'perl.ico';
    const ICON_NGROK = 'ngrok.ico';
    */

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Process PowerShell configuration.
     *
     * PowerShell 7+ uses profile scripts and Oh My Posh for configuration,
     * not XML like ConsoleZ. This method is maintained for compatibility.
     */
    public static function process()
    {
        // PowerShell uses profile scripts (Microsoft.PowerShell_profile.ps1)
        // and Oh My Posh for configuration - no processing needed here
        return true;
    }

    /**
     * Generates the tabs section.
     *
     * This function creates a structure defining various tabs and their configurations.
     * It includes multiple tab sections such as command, PowerShell, PEAR, MySQL, MariaDB,
     * PostgreSQL, Ghostscript, Git, Node.js, Composer, Perl, Python, Ruby and Ngrok.
     *
     * @return string The formatted string for the tabs section.
     */
    private static function getTabsSection()
    {
        return self::getTabCmdSection() .
            self::getTabPowerShellSection() .
            self::getTabPearSection() .
            self::getTabMysqlSection() .
            self::getTabMariadbSection() .
            self::getTabPostgresqlSection() .
            self::getTabGhostscriptSection() .
            self::getTabGitSection() .
            self::getTabNodejsSection() .
            self::getTabComposerSection() .
            self::getTabPerlSection() .
            self::getTabPythonSection() .
            self::getTabRubySection() .
            self::getTabNgrokSection();
    }

    /**
     * Generates the structure for the command tab section.
     *
     * This function creates a structure defining the command tab and its configuration.
     * It retrieves the tab title and shell command from the PowerShell tool and sets the root path.
     *
     * @return string The formatted string for the command tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabCmdSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleDefault(),
                null, // self::ICON_APP - Icon not supported in native PowerShell console
                $bearsamppTools->getPowerShell()->getShell(),
                $bearsamppRoot->getRootPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the PowerShell tab section.
     *
     * This function creates a structure defining the PowerShell tab and its configuration.
     * It retrieves the PowerShell path and sets the root path.
     *
     * @return string The formatted string for the PowerShell tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabPowerShellSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $powerShellPath = Util::getPowerShellPath();
        if ($powerShellPath !== false) {
            return self::getTab(
                    $bearsamppTools->getPowerShell()->getTabTitlePowershell(),
                    null, // self::ICON_POWERSHELL - Icon not supported in native PowerShell console
                    $powerShellPath,
                    $bearsamppRoot->getRootPath()
                ) . PHP_EOL;
        }

        return "";
    }

    /**
     * Generates the structure for the PEAR tab section.
     *
     * This function creates a structure defining the PEAR tab and its configuration.
     * It retrieves the PEAR executable path and sets the symlink path.
     *
     * @return string The formatted string for the PEAR tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Bins  $bearsamppBins The bins object of the application.
     */
    private static function getTabPearSection()
    {
        global $bearsamppBins, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppBins->getPhp()->getPearExe() . '&quot;');
        if (!file_exists($bearsamppBins->getPhp()->getPearExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppBins->getPhp()->getPearExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitlePear(),
                null, // self::ICON_PEAR - Icon not supported in native PowerShell console
                $shell,
                $bearsamppBins->getPhp()->getSymlinkPath() . '/pear'
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the MySQL tab section.
     *
     * This function creates a structure defining the MySQL tab and its configuration.
     * It retrieves the MySQL CLI executable path and sets the symlink path.
     *
     * @return string The formatted string for the MySQL tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Bins  $bearsamppBins The bins object of the application.
     */
    private static function getTabMysqlSection()
    {
        global $bearsamppBins, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppBins->getMysql()->getCliExe() . '&quot; -u' .
            $bearsamppBins->getMysql()->getRootUser() .
            ($bearsamppBins->getMysql()->getRootPwd() ? ' -p' : ''));
        if (!file_exists($bearsamppBins->getMysql()->getCliExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppBins->getMysql()->getCliExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleMysql(),
                null, // self::ICON_DB - Icon not supported in native PowerShell console
                $shell,
                $bearsamppBins->getMysql()->getSymlinkPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the MariaDB tab section.
     *
     * This function creates a structure defining the MariaDB tab and its configuration.
     * It retrieves the MariaDB CLI executable path and sets the symlink path.
     *
     * @return string The formatted string for the MariaDB tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Bins  $bearsamppBins The bins object of the application.
     */
    private static function getTabMariadbSection()
    {
        global $bearsamppBins, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppBins->getMariadb()->getCliExe() . '&quot; -u' .
            $bearsamppBins->getMariadb()->getRootUser() .
            ($bearsamppBins->getMariadb()->getRootPwd() ? ' -p' : ''));
        if (!file_exists($bearsamppBins->getMariadb()->getCliExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppBins->getMariadb()->getCliExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleMariadb(),
                null, // self::ICON_DB - Icon not supported in native PowerShell console
                $shell,
                $bearsamppBins->getMariadb()->getSymlinkPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the PostgreSQL tab section.
     *
     * This function creates a structure defining the PostgreSQL tab and its configuration.
     * It retrieves the PostgreSQL CLI executable path and sets the symlink path.
     *
     * @return string The formatted string for the PostgreSQL tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Bins  $bearsamppBins The bins object of the application.
     */
    private static function getTabPostgresqlSection()
    {
        global $bearsamppBins, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppBins->getPostgresql()->getCliExe() . '&quot;' .
            ' -h 127.0.0.1' .
            ' -p ' . $bearsamppBins->getPostgresql()->getPort() .
            ' -U ' . $bearsamppBins->getPostgresql()->getRootUser() .
            ' -d postgres');
        if (!file_exists($bearsamppBins->getPostgresql()->getCliExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppBins->getPostgresql()->getCliExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitlePostgresql(),
                null, // self::ICON_DB - Icon not supported in native PowerShell console
                $shell,
                $bearsamppBins->getPostgresql()->getSymlinkPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Git tab section.
     *
     * This function creates a structure defining the Git tab and its configuration.
     * It retrieves the Git executable path and sets the WWW path.
     *
     * @return string The formatted string for the Git tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabGitSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell();
        if (!file_exists($bearsamppTools->getGit()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getGit()->getExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleGit(),
                null, // self::ICON_GIT - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Node.js tab section.
     *
     * This function creates a structure defining the Node.js tab and its configuration.
     * It retrieves the Node.js launch path and sets the WWW path.
     *
     * @return string The formatted string for the Node.js tab section.
     * @global Bins  $bearsamppBins The bins object of the application.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabNodejsSection()
    {
        global $bearsamppRoot, $bearsamppBins, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppBins->getNodejs()->getLaunch() . '&quot;');
        if (!file_exists($bearsamppBins->getNodejs()->getLaunch())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppBins->getNodejs()->getLaunch() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleNodejs(),
                null, // self::ICON_NODEJS - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Composer tab section.
     *
     * This function creates a structure defining the Composer tab and its configuration.
     * It retrieves the Composer executable path and sets the WWW path.
     *
     * @return string The formatted string for the Composer tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabComposerSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppTools->getComposer()->getExe() . '&quot;');
        if (!file_exists($bearsamppTools->getComposer()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getComposer()->getExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleComposer(),
                null, // self::ICON_COMPOSER - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Python tab section.
     *
     * This function creates a structure defining the Python tab and its configuration.
     * It retrieves the Python executable path and sets the WWW path.
     *
     * @return string The formatted string for the Python tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabPythonSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppTools->getPython()->getExe() . '&quot;');
        if (!file_exists($bearsamppTools->getPython()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getPython()->getExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitlePython(),
                null, // self::ICON_PYTHON - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Ruby tab section.
     *
     * This function creates a structure defining the Ruby tab and its configuration.
     * It retrieves the Ruby executable path and sets the WWW path.
     *
     * @return string The formatted string for the Ruby tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabRubySection()
    {
        global $bearsamppRoot, $bearsamppTools;

        // Check if Ruby exists first
        if (!file_exists($bearsamppTools->getRuby()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getRuby()->getExe() . ' not found');
        } else {
            // Use irb (Interactive Ruby) for an interactive shell
            $rubyDir = dirname($bearsamppTools->getRuby()->getExe());
            $irbExe = $rubyDir . '/irb.bat';
            
            // Check if irb.bat exists, otherwise try irb.cmd or just use ruby with -i flag
            if (file_exists($irbExe)) {
                $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $irbExe . '&quot;');
            } elseif (file_exists($rubyDir . '/irb.cmd')) {
                $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $rubyDir . '/irb.cmd' . '&quot;');
            } elseif (file_exists($rubyDir . '/irb')) {
                $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $rubyDir . '/irb' . '&quot;');
            } else {
                // Fallback to just opening a shell in the Ruby directory
                $shell = $bearsamppTools->getPowerShell()->getShell();
            }
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleRuby(),
                null, // self::ICON_RUBY - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for the Perl tab section.
     *
     * This function creates a structure defining the Perl tab and its configuration.
     * It retrieves the Perl executable path and sets the WWW path.
     *
     * @return string The formatted string for the Perl tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     *
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabPerlSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppTools->getPerl()->getExe() . '&quot;');
        if (!file_exists($bearsamppTools->getPerl()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getPerl()->getExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitlePerl(),
                null, // self::ICON_PERL - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the tab section for Ghostscript in the console.
     *
     * This function constructs a shell command to check the version of Ghostscript
     * and verifies if the executable exists. If the executable is not found, it
     * returns a message indicating the absence of the executable. It then creates
     * a tab section with the appropriate title, icon, shell command, and initial
     * directory.
     *
     * @return string The structure for the Ghostscript tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabGhostscriptSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppTools->getGhostscript()->getExeConsole() . '&quot;');
        if (!file_exists($bearsamppTools->getGhostscript()->getExeConsole())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getGhostscript()->getExeConsole() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleGhostscript(),
                null, // self::ICON_GHOSTSCRIPT - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the tab section for Ngrok in the console.
     *
     * This function constructs a shell command to check the version of Ngrok
     * and verifies if the executable exists. If the executable is not found, it
     * returns a message indicating the absence of the executable. It then creates
     * a tab section with the appropriate title, icon, shell command, and initial
     * directory.
     *
     * @return string The structure for the Ngrok tab section.
     * @global Tools $bearsamppTools The tools object of the application.
     * @global Root  $bearsamppRoot The root object of the application.
     */
    private static function getTabNgrokSection()
    {
        global $bearsamppRoot, $bearsamppTools;

        $shell = $bearsamppTools->getPowerShell()->getShell('&quot;' . $bearsamppTools->getNgrok()->getExe() . '&quot;');
        if (!file_exists($bearsamppTools->getNgrok()->getExe())) {
            $shell = $bearsamppTools->getPowerShell()->getShell('echo ' . $bearsamppTools->getNgrok()->getExe() . ' not found');
        }

        return self::getTab(
                $bearsamppTools->getPowerShell()->getTabTitleNgrok(),
                null, // self::ICON_NGROK - Icon not supported in native PowerShell console
                $shell,
                $bearsamppRoot->getWwwPath()
            ) . PHP_EOL;
    }

    /**
     * Generates the structure for a tab.
     *
     * This function constructs the structure for a tab, including the title,
     * shell command, and initial directory.
     *
     * Note: Icon parameter is currently unused as native PowerShell consoles do not support
     * custom icons in tab titles. This was a feature of ConsoleZ which is no longer used.
     * To use icons, consider using a terminal emulator like ConEmu, Cmder, or Windows Terminal.
     *
     * @param string $title The title of the tab.
     * @param string|null $icon The icon for the tab (currently unused).
     * @param string $shell The shell command to be executed in the tab.
     * @param string $initDir The initial directory for the tab.
     * @return string The structure for the tab.
     * @global Core  $bearsamppCore The core object of the application.
     */
    private static function getTab($title, $icon, $shell, $initDir)
    {
        global $bearsamppCore;
        // Icon parameter is ignored as native PowerShell console does not support custom icons
        // Return tab information as a formatted string
        return "Title: $title | Shell: $shell | InitDir: $initDir";
    }
}
