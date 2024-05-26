<?php

class TplAppServices
{
    const ACTION_START = 'startServices';
    const ACTION_STOP = 'stopServices';
    const ACTION_RESTART = 'restartServices';
    
    public static function process()
    {
        global $bearsamppLang;
        
        $tplStart = TplApp::getActionMulti(
            self::ACTION_START, null,
            array($bearsamppLang->getValue(Lang::MENU_START_SERVICES), TplAestan::GLYPH_SERVICES_START),
            false, get_called_class()
        );
        
        $tplStop = TplApp::getActionMulti(
            self::ACTION_STOP, null,
            array($bearsamppLang->getValue(Lang::MENU_STOP_SERVICES), TplAestan::GLYPH_SERVICES_STOP),
            false, get_called_class()
        );
        
        $tplRestart = TplApp::getActionMulti(
            self::ACTION_RESTART, null,
            array($bearsamppLang->getValue(Lang::MENU_RESTART_SERVICES), TplAestan::GLYPH_SERVICES_RESTART),
            false, get_called_class()
        );
        
        // Items
        $items = $tplStart[TplApp::SECTION_CALL] . PHP_EOL .
            $tplStop[TplApp::SECTION_CALL] . PHP_EOL .
            $tplRestart[TplApp::SECTION_CALL] . PHP_EOL;
        
        // Actions
        $actions = PHP_EOL . $tplStart[TplApp::SECTION_CONTENT] .
            PHP_EOL . $tplStop[TplApp::SECTION_CONTENT] .
            PHP_EOL . $tplRestart[TplApp::SECTION_CONTENT];
        
        return array($items, $actions);
    }
    
    public static function getActionStartServices()
    {
        global $bearsamppBins;
        $actions = '';
        
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $actions .= TplService::getActionStart($service->getName()) . PHP_EOL;
        }
        
        return $actions;
    }
    
    public static function getActionStopServices()
    {
        global $bearsamppBins;
        $actions = '';
        
        foreach ($bearsamppBins->getServices() as $sName => $service) {
            $actions .= TplService::getActionStop($service->getName()) . PHP_EOL;
        }
    
        return $actions;
    }
    
    public static function getActionRestartServices()
    {
        return self::getActionStopServices() . self::getActionStartServices();
    }
}
