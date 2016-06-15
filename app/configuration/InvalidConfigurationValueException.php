<?php

namespace DBConnectionWatcher\Configuration;

class InvalidConfigurationValueException extends \Exception
{
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
