<?php

/**
 * Exception class for invalid configuration values exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Configuration;

/**
 * Class InvalidConfigurationValueException.
 *
 * @package DBConnectionWatcher\Configuration
 * @author  Julen Pardo
 */
class InvalidConfigurationValueException extends ConfigurationException
{
    /**
     * Exception message.
     * @const
     */
    const MESSAGE = "The '%1' configuration is empty.";

    /**
     * InvalidConfigurationValueException constructor.
     *
     * @param string $property The property that caused the exception.
     */
    public function __construct($property)
    {
        $message = str_replace('%1', $property, self::MESSAGE);
        parent::__construct($message);
    }
}
