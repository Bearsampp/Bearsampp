<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class ToolGit manages Git tool configurations and repository operations.
 * It extends the Module class, inheriting its basic properties and methods.
 */
class ToolGit extends Module
{
    /**
     * Configuration keys for the root and local settings.
     */
    const ROOT_CFG_VERSION = 'gitVersion';
    const LOCAL_CFG_EXE = 'gitExe';
    const LOCAL_CFG_BASH = 'gitBash';
    const LOCAL_CFG_SCAN_STARTUP = 'gitScanStartup';

    /**
     * File names for storing repository data.
     */
    const REPOS_FILE = 'repos.dat';
    const REPOS_CACHE_FILE = 'reposCache.dat';

    /**
     * @var string Path to the repository file.
     */
    private $reposFile;

    /**
     * @var string Path to the cached repository file.
     */
    private $reposCacheFile;

    /**
     * @var array List of repositories.
     */
    private $repos;

    /**
     * @var string Path to the Git executable.
     */
    private $exe;

    /**
     * @var string Path to the Git bash script.
     */
    private $bash;

    /**
     * @var bool Whether to scan for repositories at startup.
     */
    private $scanStartup;

    /**
     * Constructor initializes the ToolGit module with specific ID and type.
     * It logs the initialization and reloads the configuration.
     *
     * @param   string  $id    The module ID.
     * @param   string  $type  The module type.
     */
    public function __construct($id, $type)
    {
        Util::logInitClass( $this );
        $this->reload( $id, $type );
    }

    /**
     * Reloads the configuration from the configuration files and updates properties.
     * It also checks for the existence of necessary files and directories.
     *
     * @param   string|null  $id    Optional module ID to reload.
     * @param   string|null  $type  Optional module type to reload.
     */
    public function reload($id = null, $type = null)
    {
        global $bearsamppRoot, $bearsamppConfig, $bearsamppLang;
        Util::logReloadClass( $this );

        $this->name    = $bearsamppLang->getValue( Lang::GIT );
        $this->version = $bearsamppConfig->getRaw( self::ROOT_CFG_VERSION );
        parent::reload( $id, $type );

        $this->reposFile      = $this->symlinkPath . '/' . self::REPOS_FILE;
        $this->reposCacheFile = $this->symlinkPath . '/' . self::REPOS_CACHE_FILE;

        if ( $this->bearsamppConfRaw !== false ) {
            $this->exe         = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_EXE];
            $this->bash        = $this->symlinkPath . '/' . $this->bearsamppConfRaw[self::LOCAL_CFG_BASH];
            $this->scanStartup = $this->bearsamppConfRaw[self::LOCAL_CFG_SCAN_STARTUP];
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
        if ( !is_file( $this->reposFile ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_CONF_NOT_FOUND ), $this->name . ' ' . $this->version, $this->reposFile ) );
        }
        if ( !is_file( $this->exe ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->exe ) );
        }
        if ( !is_file( $this->bash ) ) {
            Util::logError( sprintf( $bearsamppLang->getValue( Lang::ERROR_EXE_NOT_FOUND ), $this->name . ' ' . $this->version, $this->bash ) );
        }

        if ( is_file( $this->reposFile ) ) {
            $this->repos  = explode( PHP_EOL, file_get_contents( $this->reposFile ) );
            $rebuildRepos = array();
            foreach ( $this->repos as $repo ) {
                $repo = trim( $repo );
                if ( stripos( $repo, ':' ) === false ) {
                    $repo = $bearsamppRoot->getRootPath() . '/' . $repo;
                }
                if ( is_dir( $repo ) ) {
                    $rebuildRepos[] = Util::formatUnixPath( $repo );
                }
                else {
                    Util::logWarning( $this->name . ' repository not found: ' . $repo );
                }
            }
            $this->repos = $rebuildRepos;
        }
    }

    /**
     * Updates the configuration for the Git module, executing post-install scripts and setting global configurations.
     *
     * @param   string|null  $version     Optional version to set during the update.
     * @param   int          $sub         Level of indentation for logging.
     * @param   bool         $showWindow  Whether to show any GUI window during the process.
     *
     * @return bool Returns true if the update was successful.
     */
    protected function updateConfig($version = null, $sub = 0, $showWindow = false)
    {
        global $bearsamppWinbinder;

        if ( !$this->enable ) {
            return true;
        }

        $version = $version == null ? $this->version : $version;
        Util::logDebug( ($sub > 0 ? str_repeat( ' ', 2 * $sub ) : '') . 'Update ' . $this->name . ' ' . $version . ' config' );

        if ( file_exists( $this->getSymlinkPath() . '/post-install.bat' ) ) {
            $bearsamppWinbinder->exec( $this->getBash(), '--no-needs-console --hide --no-cd --command=' . $this->getSymlinkPath() . '/post-install.bat', true );
        }

        $bearsamppWinbinder->exec( $this->getExe(), 'config --global core.autocrlf false', true );
        $bearsamppWinbinder->exec( $this->getExe(), 'config --global core.eol lf', true );

        return true;
    }

    /**
     * Finds repositories either from cache or by scanning directories.
     *
     * @param   bool  $cache  Whether to use the cached repository list.
     *
     * @return array List of found repositories.
     */
    public function findRepos($cache = true)
    {
        $result = array();

        if ( $cache ) {
            if ( file_exists( $this->reposCacheFile ) ) {
                $repos = file( $this->reposCacheFile );
                foreach ( $repos as $repo ) {
                    array_push( $result, trim( $repo ) );
                }
            }
        }
        else {
            if ( !empty( $this->repos ) ) {
                foreach ( $this->repos as $repo ) {
                    $foundRepos = Util::findRepos( $repo, $repo, '.git/config' );
                    if ( !empty( $foundRepos ) ) {
                        foreach ( $foundRepos as $foundRepo ) {
                            array_push( $result, $foundRepo );
                        }
                    }
                }
            }
            $strResult = implode( PHP_EOL, $result );
            file_put_contents( $this->reposCacheFile, $strResult );
        }

        return $result;
    }

    /**
     * Sets the version of the Git module and updates the configuration.
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
     * Gets the list of repositories.
     *
     * @return array List of repositories.
     */
    public function getRepos()
    {
        return $this->repos;
    }

    /**
     * Gets the path to the Git executable.
     *
     * @return string Path to the Git executable.
     */
    public function getExe()
    {
        return $this->exe;
    }

    /**
     * Gets the path to the Git bash script.
     *
     * @return string Path to the Git bash script.
     */
    public function getBash()
    {
        return $this->bash;
    }

    /**
     * Checks if scanning for repositories at startup is enabled.
     *
     * @return bool True if scanning is enabled, false otherwise.
     */
    public function isScanStartup()
    {
        return $this->scanStartup == Config::ENABLED;
    }

    /**
     * Sets whether to scan for repositories at startup.
     *
     * @param   bool  $scanStartup  True to enable scanning, false to disable.
     */
    public function setScanStartup($scanStartup)
    {
        $this->scanStartup = $scanStartup;
        Util::replaceInFile( $this->bearsamppConf, array(
            '/^' . self::LOCAL_CFG_SCAN_STARTUP . '/' => self::LOCAL_CFG_SCAN_STARTUP . ' = "' . $this->scanStartup . '"'
        ) );
    }
}
