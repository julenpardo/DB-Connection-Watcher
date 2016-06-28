<?php

namespace DBConnectionWatcher\Tracker;

class WriteException extends \Exception
{
    const MESSAGE = "An error occurred when trying to write to '%1': ";

    public function __construct($path)
    {
        $message = str_replace('%1', $path, self::MESSAGE);
        $message .= error_get_last()['message'];

        parent::__construct($message);
    }
}
