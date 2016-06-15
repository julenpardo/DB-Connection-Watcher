<?php

namespace DBConnectionWatcher\Configuration;

class InvalidConfigurationValueTypeException extends \Exception
{
    const MESSAGE = "Invalid type of '%1' configuration: expecting %2 type and got '%3' value, in section '%4'.";

    /**
     * InvalidConfigurationValueTypeException constructor.
     *
     * @param string $key The configuration that caused the exception.
     * @param string $expectedType The expecting type for $key value.
     * @param $value The actual value.
     * @param string $section The section where the exception occurred.
     */
    public function __construct($key, $expectedType, $value, $section)
    {
        $message = str_replace('%1', $key, self::MESSAGE);
        $message = str_replace('%2', $expectedType, $message);
        $message = str_repalce('%3', $value, $message);
        $message = str_replace('%4', $section, $message);

        parent::__construct($message);
    }
}
