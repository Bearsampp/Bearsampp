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
 * Class ActionChangePort
 * Handles the process of changing the port for various services in the Bearsampp application.
 */
class ActionChangePort
{
    private $bin;
    private $currentPort;
    private $cntProcessActions;

    private $wbWindow;

    private $wbLabelCurrent;

    private $wbLabelPort;
    private $wbInputPort;

    private $wbProgressBar;
    private $wbBtnFinish;
    private $wbBtnCancel;

    /**
     * ActionChangePort constructor.
     * Initializes the port change process for the specified service.
     *
     * @param   array  $args  The arguments passed to the constructor, where the first element specifies the service name.
     */
    public function __construct($args)
    {
        global $bearsamppLang, $bearsamppBins, $bearsamppWinbinder;

        if ( isset( $args[0] ) && !empty( $args[0] ) ) {
            $this->bin               = $bearsamppBins->getApache();
            $this->currentPort       = $bearsamppBins->getApache()->getPort();
            $this->cntProcessActions = 3;
            if ( $args[0] == $bearsamppBins->getMysql()->getName() ) {
                $this->bin               = $bearsamppBins->getMysql();
                $this->currentPort       = $bearsamppBins->getMysql()->getPort();
                $this->cntProcessActions = 3;
            }
            elseif ( $args[0] == $bearsamppBins->getMariadb()->getName() ) {
                $this->bin               = $bearsamppBins->getMariadb();
                $this->currentPort       = $bearsamppBins->getMariadb()->getPort();
                $this->cntProcessActions = 3;
            }
            elseif ( $args[0] == $bearsamppBins->getPostgresql()->getName() ) {
                $this->bin               = $bearsamppBins->getPostgresql();
                $this->currentPort       = $bearsamppBins->getPostgresql()->getPort();
                $this->cntProcessActions = 3;
            }
            elseif ( $args[0] == $bearsamppBins->getMailpit()->getName() ) {
                $this->bin               = $bearsamppBins->getMailpit();
                $this->currentPort       = $bearsamppBins->getMailpit()->getSmtpPort();
                $this->cntProcessActions = 3;
            }
            elseif ( $args[0] == $bearsamppBins->getMemcached()->getName() ) {
                $this->bin               = $bearsamppBins->getMemcached();
                $this->currentPort       = $bearsamppBins->getMemcached()->getPort();
                $this->cntProcessActions = 3;
            } elseif ($args[0] == $bearsamppBins->getXlight()->getName()) {
                $this->bin               = $bearsamppBins->getXlight();
                $this->currentPort       = $bearsamppBins->getXlight()->getPort();
                $this->cntProcessActions = 3;
            }
            elseif ( $args[0] == $bearsamppBins->getXlight()->getName() ) {
                $this->bin               = $bearsamppBins->getXlight();
                $this->currentPort       = $bearsamppBins->getXlight()->getPort();
                $this->cntProcessActions = 3;
            }

            $bearsamppWinbinder->reset();
            $this->wbWindow = $bearsamppWinbinder->createAppWindow( sprintf( $bearsamppLang->getValue( Lang::CHANGE_PORT_TITLE ), $args[0] ), 380, 170, WBC_NOTIFY, WBC_KEYDOWN | WBC_KEYUP );

            $this->wbLabelCurrent = $bearsamppWinbinder->createLabel(
                $this->wbWindow,
                sprintf( $bearsamppLang->getValue( Lang::CHANGE_PORT_CURRENT_LABEL ), $args[0], $this->currentPort ), 15, 15, 350
            );

            $this->wbLabelPort = $bearsamppWinbinder->createLabel( $this->wbWindow, $bearsamppLang->getValue( Lang::CHANGE_PORT_NEW_LABEL ) . ' :', 15, 45, 85, null, WBC_RIGHT );
            $this->wbInputPort = $bearsamppWinbinder->createInputText( $this->wbWindow, $this->currentPort, 105, 43, 50, null, 5, WBC_NUMBER );

            $this->wbProgressBar = $bearsamppWinbinder->createProgressBar( $this->wbWindow, $this->cntProcessActions + 1, 15, 107, 170 );
            $this->wbBtnFinish   = $bearsamppWinbinder->createButton( $this->wbWindow, $bearsamppLang->getValue( Lang::BUTTON_FINISH ), 190, 102 );
            $this->wbBtnCancel   = $bearsamppWinbinder->createButton( $this->wbWindow, $bearsamppLang->getValue( Lang::BUTTON_CANCEL ), 277, 102 );

            $bearsamppWinbinder->setHandler( $this->wbWindow, $this, 'processWindow' );
            $bearsamppWinbinder->setFocus( $this->wbInputPort[WinBinder::CTRL_OBJ] );
            $bearsamppWinbinder->mainLoop();
            $bearsamppWinbinder->reset();
        }
    }

    /**
     * Processes window events and handles user interactions.
     *
     * @param   mixed  $window  The window object.
     * @param   int    $id      The control ID.
     * @param   mixed  $ctrl    The control object.
     * @param   mixed  $param1  Additional parameter 1.
     * @param   mixed  $param2  Additional parameter 2.
     */
    public function processWindow($window, $id, $ctrl, $param1, $param2)
    {
        global $bearsamppLang, $bearsamppWinbinder;
        $boxTitle = sprintf( $bearsamppLang->getValue( Lang::CHANGE_PORT_TITLE ), $this->bin );
        $port     = $bearsamppWinbinder->getText( $this->wbInputPort[WinBinder::CTRL_OBJ] );

        switch ( $id ) {
            case $this->wbInputPort[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->setEnabled( $this->wbBtnFinish[WinBinder::CTRL_OBJ], empty( $port ) ? false : true );
                break;
            case $this->wbBtnFinish[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->incrProgressBar( $this->wbProgressBar );
                if ( $port == $this->currentPort ) {
                    $bearsamppWinbinder->messageBoxWarning( $bearsamppLang->getValue( Lang::CHANGE_PORT_SAME_ERROR ), $boxTitle );
                    $bearsamppWinbinder->resetProgressBar( $this->wbProgressBar );
                    break;
                }
                $changePort = $this->bin->changePort( $port, true, $this->wbProgressBar );
                if ( $changePort === true ) {
                    $this->bin->getService()->restart();

                    $bearsamppWinbinder->messageBoxInfo(
                        sprintf( $bearsamppLang->getValue( Lang::PORT_CHANGED ), $this->bin, $port ),
                        $boxTitle
                    );
                    $bearsamppWinbinder->destroyWindow( $window );
                }
                else {
                    $bearsamppWinbinder->messageBoxError(
                        sprintf( $bearsamppLang->getValue( Lang::PORT_NOT_USED_BY ), $port, $changePort ),
                        $boxTitle
                    );
                    $bearsamppWinbinder->resetProgressBar( $this->wbProgressBar );
                }
                break;
            case IDCLOSE:
            case $this->wbBtnCancel[WinBinder::CTRL_ID]:
                $bearsamppWinbinder->destroyWindow( $window );
                break;
        }
    }
}
