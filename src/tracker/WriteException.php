<?php

/**
 * Exception class for database exceed writing in file exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Tracker;

/**
 * Class WriteException.
 *
 * @package DBConnectionWatcher\Tracker
 * @author  Julen Pardo
 */
class WriteException extends \Exception
{
    /**
     * Exception message.
     * @const
     */
    const MESSAGE = "An error occurred when trying to write to '%1': ";

    /**
     * WriteException constructor.
     *
     * @param string $path The path to the file where it has been tried to write.
     */
    public function __construct($path)
    {
        $message = str_replace('%1', $path, self::MESSAGE);
        $message .= error_get_last()['message'];

        parent::__construct($message);
    }
}
