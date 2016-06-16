<?php

namespace DBConnectionWatcher\Configuration;

class FileNotFoundException extends ConfigurationException
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
