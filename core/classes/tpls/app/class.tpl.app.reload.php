<?php

/**
 * Manages reload action templates and execution for Bearsampp menu system
 */
class TplAppReload
{
    /**
     * @var string Action identifier for reload operations
     */
    const ACTION = 'reload';

    /**
     * Generates multi-action menu item for reload functionality
     *
     * @global Lang $bearsamppLang Bearsampp language configuration instance
     * @return array Array structure for TplApp::getActionMulti containing:
     *               - Action identifier
     *               - Action parameters
     *               - Menu item configuration (label + glyph)
     *               - Disabled state
     *               - Calling class name
     */
    public static function process(): array
    {
        global $bearsamppLang;
        return TplApp::getActionMulti(
            self::ACTION,
            null,
            [$bearsamppLang->getValue(Lang::RELOAD), TplAestan::GLYPH_RELOAD],
            false,
            get_called_class()
        );
    }

    /**
     * Builds sequence of actions for configuration reload
     *
     * @return string Concatenated action sequence containing:
     *               1. PHP process execution command
     *               2. Service reset command
     *               3. Configuration reload command
     */
    public static function getActionReload(): string
    {
        return implode("\n", [
            TplApp::getActionRun(Action::RELOAD),
            'Action: resetservices',
            'Action: readconfig'
        ]);
    }

    /**
     * Executes reload sequence and returns action string
     *
     * @param mixed|null $args Arguments to pass to reload action
     * @return string Generated INI action sequence
     * @throws Exception If reload operation fails
     *
     * @log TRACE: Logs method entry and generated action content
     * @log ERROR: Captures and logs any exceptions during reload
     */
    public static function triggerReload($args = null): string
    {
        Util::logTrace('ENTERING triggerReload..');

        try {
            new ActionReload($args);
            $actionContent = self::getActionReload();
            Util::logTrace('Generated reload actions: ' . $actionContent);
            return $actionContent;

        } catch (Exception $e) {
            Util::logError('Reload failed: ' . $e->getMessage());
            return '';
        }
    }
}
