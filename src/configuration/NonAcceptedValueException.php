<?php

/**
 * Exception class for non accepted values exceptions.
 *
 * @author Julen Pardo
 */

namespace DBConnectionWatcher\Configuration;

/**
 * Class NonAcceptedValueException.
 *
 * @package DBConnectionWatcher\Configuration
 * @author  Julen Pardo
 */
class NonAcceptedValueException extends ConfigurationException
{
    /**
     * Exception message.
     * @const
     */
    const MESSAGE = "Invalid value for '%1' configuration, in section '%2', must be one of: %3";

    /**
     * NonAcceptedValueException constructor.
     *
     * @param string $key The configuration key, property, that caused the exception.
     * @param string $section The section where the exception has occurred.
     * @param array $acceptedValuesArray The array of accepted values.
     */
    public function __construct($key, $section, $acceptedValuesArray)
    {
        $message = str_replace('%1', $key, self::MESSAGE);
        $message = str_replace('%2', $section, $message);

        $acceptedValues = implode(',', $acceptedValuesArray);

        $message = str_replace('%3', $acceptedValues, $message);

        parent::__construct($message);
    }
}
