<?php

namespace DBConnectionWatcher\Configuration;

class NonAcceptedValueException extends ConfigurationException
{
    const MESSAGE = "Invalid value for '%1' configuration, in section '%2', must be one of: %3";

    public function __construct($key, $section, $acceptedValuesArray)
    {
        $message = str_replace('%1', $key, self::MESSAGE);
        $message = str_replace('%2', $section, $message);

        $acceptedValues = implode(',', $acceptedValuesArray);

        $message = str_replace('%3', $acceptedValues, $message);

        parent::__construct($message);
    }
}
