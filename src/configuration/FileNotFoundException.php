<?php

/**
 * Exception class for not found files exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Configuration;

/**
 * Class FileNotFoundException.
 *
 * @package DBConnectionWatcher\Configuration
 * @author  Julen Pardo
 */
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
