<?php

namespace DBConnectionWatcher\Configuration;

class InvalidConfigurationPropertyException extends ConfigurationException
{
    const MESSAGE = "Invalid '%1' configuration in '%2' section.";

    /**
     * InvalidConfigurationPropertyException constructor.
     *
     * @param string $property The property that caused the exception.
     * @param int $section The section when the exception has occurred.
     */
    public function __construct($property, $section)
    {
        $message = str_replace('%1', $property, self::MESSAGE);
        $message = str_replace('%2', $section, $message);
        parent::__construct($message);
    }
}
