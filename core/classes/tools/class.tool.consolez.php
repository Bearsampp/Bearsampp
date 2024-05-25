<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolConsoleZ
 * Manages the configuration and operations related to the ConsoleZ tool within the Bearsampp environment.
 */
class ToolConsoleZ extends Module
{
    /**
     * Configuration keys specific to ConsoleZ.
     */
    const ROOT_CFG_VERSION = 'consolezVersion';
    const LOCAL_CFG_EXE = 'consolezExe';
    const LOCAL_CFG_CONF = 'consolezConf';
    const LOCAL_CFG_LAUNCH_EXE = 'consolezLaunchExe';
    const LOCAL_CFG_ROWS = 'consolezRows';
    const LOCAL_CFG_COLS = 'consolezCols';

    /**
     * @var string Path to the executable file.
     */
    private $exe;

    /**
     * @var string Path to the launch executable file.
     */
    private $launchExe;

    /**
     * @var string Path to the configuration file.
     */
    private $conf;

    /**
     * @var int Number of rows in the console.
     */
    private $rows;

    /**
     * @var int Number of columns in the console.
     */
    private $cols;

    /**
     * Constructor for ToolConsoleZ.
     * Initializes the tool by loading its configuration.
     *
     * @param   string  $id    The identifier for the module.
     * @param   string  $type  The type of the module.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the configuration for the tool.
     * This method is used to refresh the tool's settings and validate its environment.
     *
     * @param   string|null  $id    Optional identifier for the module.
     * @param   string|null  $type  Optional type of the module.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::CONSOLEZ );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe       = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->launchExe = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_LAUNCH_EXE];
            $this->conf      = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_CONF];
            $this->rows      = intval( $this->bearsamppConfRaw[self::LOCAL_CFG_ROWS] );
            $this->cols      = intval( $this->bearsamppConfRaw[self::LOCAL_CFG_COLS] );
        }

        if ( !$this->enable ) {
            Util::logInfo( $this->name . ' is not enabled!' );

            return;
        }
        if ( !is_dir( $this->currentPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->currentPath ) );
        }
        if ( !is_dir( $this->symlinkPath ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_FILE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->symlinkPath ) );

            return;
        }
        if ( !is_file( $this->bearsamppConf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->bearsamppConf ) );
        }
        if ( !is_file( $this->exe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->exe ) );
        }
        if ( !is_file( $this->launchExe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->launchExe ) );
        }
        if ( !is_file( $this->conf ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->conf ) );
        }
        if ( !is_numeric( $this->rows ) || $this->rows <= 0 ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_ROWS, $this->rows ) );
        }
        if ( !is_numeric( $this->cols ) || $this->cols <= 0 ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_INVALID_PARAMETER ), self::LOCAL_CFG_COLS, $this->cols ) );
        }
    }

    /**
     * Sets the version of the tool and updates its configuration.
     *
     * @param   string  $version  The new version to set.
     */
    public function setVersion($version)
    {
        global $bearsamppConfig;
        $this->version = $version;
        $bearsamppConfig->replace( self::ROOT_CFG_VERSION, $version );
        $this->reload();
    }

    /**
     * Gets the path to the executable file.
     *
     * @return string Path to the executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Gets the path to the launch executable file.
     *
     * @return string Path to the launch executable.
     */
    public function getLaunchExe()
    {
        return $this->launchExe;
    }

    /**
     * Gets the path to the configuration file.
     *
     * @return string Path to the configuration file.
     */
    public function getConf()
    {
        return $this->conf;
    }

    /**
     * Gets the number of rows configured for the console.
     *
     * @return int Number of rows.
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Gets the number of columns configured for the console.
     *
     * @return int Number of columns.
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * Constructs a command to open a shell with optional arguments.
     *
     * @param   string|null  $args  Optional arguments to pass to the shell.
     *
     * @return string The command to execute.
     */
    public function getShell($args = null)
    {
        if ( empty( $args ) ) {
            return 'cmd /k &quot;' . Util::formatWindowsPath( $this->launchExe ) . '&quot;';
        }
        else {
            return 'cmd /k &quot;&quot;' . Util::formatWindowsPath( $this->getLaunchExe() ) . '&quot; &amp; ' . Util::formatWindowsPath( $args ) . '&quot;';
        }
    }

    /**
     * Retrieves the default tab title for the console.
     *
     * @return string The default tab title.
     */
    public function getTabTitleDefault()
    {
        global $bearsamppLang;

        return $bearsamppLang->getValue( Lang::CONSOLE );
    }

    /**
     * Returns the title for the PowerShell tab.
     *
     * @return string The title of the PowerShell tab.
     */
    public function getTabTitlePowershell()
    {
        return 'PowerShell';
    }

    /**
     * Returns the title for the PEAR tab, including the PEAR version.
     *
     * @return string The title of the PEAR tab with version.
     * @global object $bearsamppBins Bins handler.
     * @global object $bearsamppLang Language handler.
     */
    public function getTabTitlePear()
    {
        global $bearsamppLang, $bearsamppBins;

        return $bearsamppLang->getValue( Lang::PEAR ) . ' ' . $bearsamppBins->getPhp()->getPearVersion( true );
    }

    /**
     * Returns the title for the MySQL tab, including the MySQL version.
     *
     * @return string The title of the MySQL tab with version.
     * @global object $bearsamppBins Bins handler.
     * @global object $bearsamppLang Language handler.
     */
    public function getTabTitleMysql()
    {
        global $bearsamppLang, $bearsamppBins;

        return $bearsamppLang->getValue( Lang::MYSQL ) . ' ' . $bearsamppBins->getMysql()->getVersion();
    }

    /**
     * Returns the title for the MariaDB tab, including the MariaDB version.
     *
     * @return string The title of the MariaDB tab with version.
     * @global object $bearsamppBins Bins handler.
     * @global object $bearsamppLang Language handler.
     */
    public function getTabTitleMariadb()
    {
        global $bearsamppLang, $bearsamppBins;

        return $bearsamppLang->getValue( Lang::MARIADB ) . ' ' . $bearsamppBins->getMariadb()->getVersion();
    }

    /**
     * Returns the title for the PostgreSQL tab, including the PostgreSQL version.
     *
     * @return string The title of the PostgreSQL tab with version.
     * @global object $bearsamppBins Bins handler.
     * @global object $bearsamppLang Language handler.
     */
    public function getTabTitlePostgresql()
    {
        global $bearsamppLang, $bearsamppBins;

        return $bearsamppLang->getValue( Lang::POSTGRESQL ) . ' ' . $bearsamppBins->getPostgresql()->getVersion();
    }

    /**
     * Returns the title for the Git tab, including the Git version and optionally the repository name.
     *
     * @param   string|null  $repoPath       Optional. The path to the repository.
     *
     * @return string The title of the Git tab with version and repository name.
     * @global object        $bearsamppLang  Language handler.
     * @global object        $bearsamppTools Tools handler.
     */
    public function getTabTitleGit($repoPath = null)
    {
        global $bearsamppLang, $bearsamppTools;
        $result = $bearsamppLang->getValue( Lang::GIT ) . ' ' . $bearsamppTools->getGit()->getVersion();
        if ( $repoPath != null ) {
            $result .= ' - ' . basename( $repoPath );
        }

        return $result;
    }

    /**
     * Returns the title for the Node.js tab, including the Node.js version.
     *
     * @return string The title of the Node.js tab with version.
     * @global object $bearsamppBins Bins handler.
     * @global object $bearsamppLang Language handler.
     */
    public function getTabTitleNodejs()
    {
        global $bearsamppLang, $bearsamppBins;

        return $bearsamppLang->getValue( Lang::NODEJS ) . ' ' . $bearsamppBins->getNodejs()->getVersion();
    }

    /**
     * Returns the title for the Composer tab, including the Composer version.
     *
     * @return string The title of the Composer tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitleComposer()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::COMPOSER ) . ' ' . $bearsamppTools->getComposer()->getVersion();
    }

    /**
     * Returns the title for the Python tab, including the Python version.
     *
     * @return string The title of the Python tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitlePython()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::PYTHON ) . ' ' . $bearsamppTools->getPython()->getVersion();
    }

    /**
     * Returns the title for the Ruby tab, including the Ruby version.
     *
     * @return string The title of the Ruby tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitleRuby()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::RUBY ) . ' ' . $bearsamppTools->getRuby()->getVersion();
    }

    /**
     * Returns the title for the Yarn tab, including the Yarn version.
     *
     * @return string The title of the Yarn tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitleYarn()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::YARN ) . ' ' . $bearsamppTools->getYarn()->getVersion();
    }

    /**
     * Returns the title for the Perl tab, including the Perl version.
     *
     * @return string The title of the Perl tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitlePerl()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::PERL ) . ' ' . $bearsamppTools->getPerl()->getVersion();
    }

    /**
     * Returns the title for the Ghostscript tab, including the Ghostscript version.
     *
     * @return string The title of the Ghostscript tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitleGhostscript()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::GHOSTSCRIPT ) . ' ' . $bearsamppTools->getGhostscript()->getVersion();
    }

    /**
     * Returns the title for the Ngrok tab, including the Ngrok version.
     *
     * @return string The title of the Ngrok tab with version.
     * @global object $bearsamppTools Tools handler.
     * @global object $bearsamppLang  Language handler.
     */
    public function getTabTitleNgrok()
    {
        global $bearsamppLang, $bearsamppTools;

        return $bearsamppLang->getValue( Lang::NGROK ) . ' ' . $bearsamppTools->getNgrok()->getVersion();
    }
}
