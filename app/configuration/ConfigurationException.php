<?php

namespace DBConnectionWatcher\Configuration;

class ConfigurationException extends \Exception
{
    /**
     * ConfigurationException constructor.
     *
     * @param String $message Exception message.
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
