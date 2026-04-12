<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: Bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

/**
 * Class CommandRunner
 *
 * Centralizes all PHP shell-execution primitives (proc_open, popen, shell_exec)
 * so that every external command goes through a single, auditable point with
 * consistent argument escaping and logging.
 *
 * Three execution modes:
 *  - exec()      capture stdout/stderr, hidden console window  (replaces proc_open)
 *  - stream()    stream output line-by-line via callback       (replaces popen)
 *  - shellExec() capture output from a fully-formed command    (replaces shell_exec)
 *
 * Rules:
 *  - exec() and stream() always escapeshellarg() every argument individually.
 *  - shellExec() is reserved for hardcoded commands with no dynamic user input.
 *    Pass dynamic arguments through exec() or stream() instead.
 */
class CommandRunner
{
    /**
     * Writes a log entry to the batch log file.
     *
     * @param string $log The message to log.
     */
    private static function writeLog(string $log): void
    {
        global $bearsamppRoot;
        Util::logDebug($log, $bearsamppRoot->getBatchLogFilePath());
    }

    /**
     * Execute an executable with arguments, capturing stdout and stderr.
     *
     * Each argument is individually escaped with escapeshellarg(). The console
     * window is hidden on Windows via bypass_shell + CREATE_NO_WINDOW semantics.
     *
     * @param string $executable Path to the executable (will be escapeshellarg'd).
     * @param array  $args       Arguments, each will be escapeshellarg'd.
     * @param string $stderr     Populated with any stderr output on return.
     * @return string|false stdout output on success, false if the process could not start.
     */
    public static function exec(string $executable, array $args = [], string &$stderr = ''): string|false
    {
        $cmd = escapeshellarg($executable);
        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg((string) $arg);
        }

        self::writeLog('CommandRunner::exec: ' . $cmd);

        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($cmd, $descriptorspec, $pipes, null, null, ['bypass_shell' => true]);

        if (!is_resource($process)) {
            self::writeLog('CommandRunner::exec: failed to start process: ' . $cmd);
            return false;
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        if (!empty($stderr)) {
            self::writeLog('CommandRunner::exec stderr: ' . $stderr);
        }

        return $output;
    }

    /**
     * Execute an executable with arguments, combining stdout and stderr.
     *
     * Convenience wrapper around exec() for callers that need both streams merged
     * (e.g. sc.exe which may send status messages to either stream).
     *
     * @param string $executable Path to the executable (will be escapeshellarg'd).
     * @param array  $args       Arguments, each will be escapeshellarg'd.
     * @return string|false Combined output, or false if the process could not start.
     */
    public static function execCombined(string $executable, array $args = []): string|false
    {
        $stderr = '';
        $output = self::exec($executable, $args, $stderr);

        if ($output === false) {
            return false;
        }

        if (!empty($stderr)) {
            $output .= "\n" . $stderr;
        }

        return $output;
    }

    /**
     * Execute an executable with arguments, streaming output line-by-line.
     *
     * Useful for long-running processes that emit progress information.
     * Each argument is individually escaped with escapeshellarg(). Lines are
     * split on carriage-return (\r) to match Windows progress-reporting conventions.
     * Any data remaining in the buffer after EOF is flushed as a final line.
     *
     * @param string   $executable   Path to the executable (will be escapeshellarg'd).
     * @param array    $args         Arguments, each will be escapeshellarg'd.
     * @param callable $lineCallback Invoked with each trimmed output line as a string.
     * @return int|false Process exit code on success, false if the process could not start.
     */
    public static function stream(string $executable, array $args, callable $lineCallback): int|false
    {
        $cmd = escapeshellarg($executable);
        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg((string) $arg);
        }

        self::writeLog('CommandRunner::stream: ' . $cmd);

        $process = popen($cmd, 'rb');
        if (!$process) {
            self::writeLog('CommandRunner::stream: failed to start process: ' . $cmd);
            return false;
        }

        $buffer = '';
        while (!feof($process)) {
            $buffer .= fread($process, 8192);
            while (($pos = strpos($buffer, "\r")) !== false) {
                $line   = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);
                $lineCallback($line);
            }
        }

        // Flush any remaining data not terminated by \r
        if (!empty($buffer)) {
            $lineCallback(trim($buffer));
        }

        return pclose($process);
    }

    /**
     * Launch a command in the background without waiting for output.
     *
     * Uses the Windows "start /B" cmd.exe idiom to detach the process immediately.
     * The caller is responsible for ensuring $command is properly constructed;
     * use escapeshellarg() on any dynamic values before passing them in.
     *
     * @param string $command Fully-formed command string to run in the background.
     */
    public static function background(string $command): void
    {
        self::writeLog('CommandRunner::background: ' . $command);
        pclose(popen('start /B ' . $command, 'r'));
    }

    /**
     * Execute a fully-formed shell command and return its output.
     *
     * Only use this for commands that contain no dynamic user input (e.g. hardcoded
     * system queries such as "net session" or "whoami /groups"). For commands with
     * dynamic arguments, use exec() or stream() instead so that escaping is enforced
     * at the boundary.
     *
     * @param string $command Fully-formed command string. Must not contain unescaped user input.
     * @return string|null Command output, or null if the command could not be run.
     */
    public static function shellExec(string $command): ?string
    {
        self::writeLog('CommandRunner::shellExec: ' . $command);
        return shell_exec($command);
    }
}
