<?php

/**
 * Class TplAppReload
 *
 * Manages reload action templates and execution for the Bearsampp menu system.
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
     * @return array Array structure for TplApp::getActionMulti containing:
     *               - Action identifier
     *               - Action parameters
     *               - Menu item configuration (label + glyph)
     *               - Disabled state
     *               - Calling class name
     * @global Lang $bearsamppLang Bearsampp language configuration instance
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
     * @param   mixed|null  $args  Arguments to pass to the reload action
     *
     * @return string Generated reload action sequence, or an empty string if the reload fails
     *
     * @log TRACE: Logs method entry and generated action content
     * @log ERROR: Captures and logs any exceptions during reload
     */
    public static function triggerReload($args = null): string
    {
        Log::trace('ENTERING triggerReload..');

        try {
            new ActionReload($args);
            $actionContent = self::getActionReload();
            Log::trace('Generated reload actions: ' . $actionContent);

            return $actionContent;
        } catch (Exception $e) {
            Log::error('Reload failed: ' . $e->getMessage());

            return '';
        }
    }
}

