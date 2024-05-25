<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class Tools manages the initialization and operations of various tool modules.
 * It provides methods to access and update configurations for each tool.
 */
class Tools
{
    /**
     * Constant representing the type of tools managed by this class.
     */
    const TYPE = 'tools';

    /**
     * @var ToolComposer|null Instance of ToolComposer or null if not initialized.
     */
    private $composer;

    /**
     * @var ToolConsoleZ|null Instance of ToolConsoleZ or null if not initialized.
     */
    private $consolez;

    /**
     * @var ToolGhostscript|null Instance of ToolGhostscript or null if not initialized.
     */
    private $ghostscript;

    /**
     * @var ToolGit|null Instance of ToolGit or null if not initialized.
     */
    private $git;

    /**
     * @var ToolNgrok|null Instance of ToolNgrok or null if not initialized.
     */
    private $ngrok;

    /**
     * @var ToolPerl|null Instance of ToolPerl or null if not initialized.
     */
    private $perl;

    /**
     * @var ToolPython|null Instance of ToolPython or null if not initialized.
     */
    private $python;

    /**
     * @var ToolRuby|null Instance of ToolRuby or null if not initialized.
     */
    private $ruby;

    /**
     * @var ToolXdc|null Instance of ToolXdc or null if not initialized.
     */
    private $xdc;

    /**
     * @var ToolYarn|null Instance of ToolYarn or null if not initialized.
     */
    private $yarn;

    /**
     * Constructor for the Tools class.
     */
    public function __construct()
    {
    }

    /**
     * Updates the configuration for all initialized tools.
     */
    public function update()
    {
        Util::logInfo( 'Update tools config' );
        foreach ( $this->getAll() as $tool ) {
            $tool->update();
        }
    }

    /**
     * Retrieves an array of all tool instances, initializing them if necessary.
     *
     * @return array Array of tool instances.
     */
    public function getAll()
    {
        return array(
            $this->getComposer(),
            $this->getConsoleZ(),
            $this->getGhostscript(),
            $this->getGit(),
            $this->getNgrok(),
            $this->getPerl(),
            $this->getPython(),
            $this->getRuby(),
            $this->getXdc(),
            $this->getYarn(),
        );
    }

    /**
     * Gets the instance of ToolComposer, creating it if it does not exist.
     *
     * @return ToolComposer The instance of ToolComposer.
     */
    public function getComposer()
    {
        if ( $this->composer == null ) {
            $this->composer = new ToolComposer( 'composer', self::TYPE );
        }

        return $this->composer;
    }

    /**
     * Retrieves the instance of ToolConsoleZ, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolConsoleZ throughout the application lifecycle.
     *
     * @return ToolConsoleZ The instance of ToolConsoleZ.
     */
    public function getConsoleZ()
    {
        if ( $this->consolez == null ) {
            $this->consolez = new ToolConsoleZ( 'consolez', self::TYPE );
        }

        return $this->consolez;
    }

    /**
     * Retrieves the instance of ToolGhostscript, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolGhostscript throughout the application lifecycle.
     *
     * @return ToolGhostscript The instance of ToolGhostscript.
     */
    public function getGhostscript()
    {
        if ( $this->ghostscript == null ) {
            $this->ghostscript = new ToolGhostscript( 'ghostscript', self::TYPE );
        }

        return $this->ghostscript;
    }

    /**
     * Retrieves the instance of ToolGit, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolGit throughout the application lifecycle.
     *
     * @return ToolGit The instance of ToolGit.
     */
    public function getGit()
    {
        if ( $this->git == null ) {
            $this->git = new ToolGit( 'git', self::TYPE );
        }

        return $this->git;
    }

    /**
     * Retrieves the instance of ToolGit configured for the Git GUI, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolGit for the Git GUI throughout the application lifecycle.
     *
     * @return ToolGit The instance of ToolGit configured for the Git GUI.
     */
    public function getGitGui()
    {
        if ( $this->git == null ) {
            $this->git = new ToolGit( 'git-gui', self::TYPE );
        }

        return $this->git;
    }

    /**
     * Retrieves the instance of ToolNgrok, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolNgrok throughout the application lifecycle.
     *
     * @return ToolNgrok The instance of ToolNgrok.
     */
    public function getNgrok()
    {
        if ( $this->ngrok == null ) {
            $this->ngrok = new ToolNgrok( 'ngrok', self::TYPE );
        }

        return $this->ngrok;
    }

    /**
     * Retrieves the instance of ToolPerl, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolPerl throughout the application lifecycle.
     *
     * @return ToolPerl The instance of ToolPerl.
     */
    public function getPerl()
    {
        if ( $this->perl == null ) {
            $this->perl = new ToolPerl( 'perl', self::TYPE );
        }

        return $this->perl;
    }

    /**
     * Retrieves the instance of ToolPython, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolPython throughout the application lifecycle.
     *
     * @return ToolPython The instance of ToolPython.
     */
    public function getPython()
    {
        if ( $this->python == null ) {
            $this->python = new ToolPython( 'python', self::TYPE );
        }

        return $this->python;
    }

    /**
     * Retrieves the instance of ToolRuby, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolRuby throughout the application lifecycle.
     *
     * @return ToolRuby The instance of ToolRuby.
     */
    public function getRuby()
    {
        if ( $this->ruby == null ) {
            $this->ruby = new ToolRuby( 'ruby', self::TYPE );
        }

        return $this->ruby;
    }

    /**
     * Retrieves the instance of ToolXdc, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolXdc throughout the application lifecycle.
     *
     * @return ToolXdc The instance of ToolXdc.
     */
    public function getXdc()
    {
        if ( $this->xdc == null ) {
            $this->xdc = new ToolXdc( 'xdc', self::TYPE );
        }

        return $this->xdc;
    }

    /**
     * Retrieves the instance of ToolYarn, creating it if it does not exist.
     * This method ensures that there is only one instance of ToolYarn throughout the application lifecycle.
     * It uses the singleton pattern to manage the instance of ToolYarn.
     *
     * @return ToolYarn The instance of ToolYarn.
     */
    public function getYarn()
    {
        if ( $this->yarn == null ) {
            $this->yarn = new ToolYarn( 'yarn', self::TYPE );
        }

        return $this->yarn;
    }
}
