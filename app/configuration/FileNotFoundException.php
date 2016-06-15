<?php

namespace DBConnectionWatcher\Configuration;

class FileNotFoundException extends \Exception
{
    /**
     * FileNotFoundException constructor.
     *
     * @param String $message Exception message.
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
