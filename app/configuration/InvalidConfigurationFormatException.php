<?php

namespace DBConnectionWatcher\Configuration;

class InvalidConfigurationFormatException extends ConfigurationException
{
    const MESSAGE = 'The file has an invalid format (may you forgot to put a [section]?).';

    /**
     * InvalidConfigurationFormatException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}
