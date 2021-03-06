<?php

/**
 * General exception class for configuration parsing exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Configuration;

/**
 * Class ConfigurationException.
 *
 * @package DBConnectionWatcher\Configuration
 * @author  Julen Pardo
 */
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
