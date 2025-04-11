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
 * Class Tools
 *
 * This class manages various tool modules in the Bearsampp application.
 * It provides methods to retrieve and update the configuration of these tools.
 */
class Tools
{
    /**
     * The type of the tools.
     */
    const TYPE = 'tools';

    /**
     * @var ToolComposer|null The Composer tool instance.
     */
    private $composer;

    /**
     * @var ToolBruno|null The Bruno tool instance.
     */
    private $bruno;

    /**
     * @var ToolConsoleZ|null The ConsoleZ tool instance.
     */
    private $consolez;

    /**
     * @var ToolGhostscript|null The Ghostscript tool instance.
     */
    private $ghostscript;

    /**
     * @var ToolGit|null The Git tool instance.
     */
    private $git;

    /**
     * @var ToolNgrok|null The Ngrok tool instance.
     */
    private $ngrok;

    /**
     * @var ToolPerl|null The Perl tool instance.
     */
    private $perl;

    /**
     * @var ToolPython|null The Python tool instance.
     */
    private $python;

    /**
     * @var ToolRuby|null The Ruby tool instance.
     */
    private $ruby;

    /**
     * Constructor for the Tools class.
     */
    public function __construct()
    {
    }

    /**
     * Updates the configuration of all tools.
     */
    public function update()
    {
        Util::logInfo('Update tools config');
        foreach ($this->getAll() as $tool) {
            $tool->update();
        }
    }

    /**
     * Retrieves all tool instances.
     *
     * @return array An array of all tool instances.
     */
    public function getAll()
    {
        return array(
            $this->getBruno(),
            $this->getComposer(),
            $this->getConsoleZ(),
            $this->getGhostscript(),
            $this->getGit(),
            $this->getNgrok(),
            $this->getPerl(),
            $this->getPython(),
            $this->getRuby(),
        );
    }

    /**
     * Retrieves the Bruno tool instance.
     *
     * @return ToolBruno The Bruno tool instance.
     */
    public function getBruno()
    {
        if ($this->bruno == null) {
            $this->bruno = new ToolBruno('bruno', self::TYPE);
        }
        return $this->bruno;
    }

    /**
     * Retrieves the Composer tool instance.
     *
     * @return ToolComposer The Composer tool instance.
     */
    public function getComposer()
    {
        if ($this->composer == null) {
            $this->composer = new ToolComposer('composer', self::TYPE);
        }
        return $this->composer;
    }

    /**
     * Retrieves the ConsoleZ tool instance.
     *
     * @return ToolConsoleZ The ConsoleZ tool instance.
     */
    public function getConsoleZ()
    {
        if ($this->consolez == null) {
            $this->consolez = new ToolConsoleZ('consolez', self::TYPE);
        }
        return $this->consolez;
    }

    /**
     * Retrieves the Ghostscript tool instance.
     *
     * @return ToolGhostscript The Ghostscript tool instance.
     */
    public function getGhostscript()
    {
        if ($this->ghostscript == null) {
            $this->ghostscript = new ToolGhostscript('ghostscript', self::TYPE);
        }
        return $this->ghostscript;
    }

    /**
     * Retrieves the Git tool instance.
     *
     * @return ToolGit The Git tool instance.
     */
    public function getGit()
    {
        if ($this->git == null) {
            $this->git = new ToolGit('git', self::TYPE);
        }
        return $this->git;
    }

    /**
     * Retrieves the Git GUI tool instance.
     *
     * @return ToolGit The Git GUI tool instance.
     */
    public function getGitGui()
    {
        if ($this->git == null) {
            $this->git = new ToolGit('git-gui', self::TYPE);
        }
        return $this->git;
    }

    /**
     * Retrieves the Ngrok tool instance.
     *
     * @return ToolNgrok The Ngrok tool instance.
     */
    public function getNgrok()
    {
        if ($this->ngrok == null) {
            $this->ngrok = new ToolNgrok('ngrok', self::TYPE);
        }
        return $this->ngrok;
    }

    /**
     * Retrieves the Perl tool instance.
     *
     * @return ToolPerl The Perl tool instance.
     */
    public function getPerl()
    {
        if ($this->perl == null) {
            $this->perl = new ToolPerl('perl', self::TYPE);
        }
        return $this->perl;
    }

    /**
     * Retrieves the Python tool instance.
     *
     * @return ToolPython The Python tool instance.
     */
    public function getPython()
    {
        if ($this->python == null) {
            $this->python = new ToolPython('python', self::TYPE);
        }
        return $this->python;
    }

    /**
     * Retrieves the Ruby tool instance.
     *
     * @return ToolRuby The Ruby tool instance.
     */
    public function getRuby()
    {
        if ($this->ruby == null) {
            $this->ruby = new ToolRuby('ruby', self::TYPE);
        }
        return $this->ruby;
    }
}
