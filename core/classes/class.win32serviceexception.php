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
 * Class Win32ServiceException
 *
 * This class represents exceptions thrown by Win32 service operations.
 * It extends the base Exception class to provide specific error handling for Win32 service functions.
 */
class Win32ServiceException extends \Exception
{
    /**
     * Constructor for the Win32ServiceException class.
     *
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param \Throwable|null $previous The previous throwable used for exception chaining.
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
