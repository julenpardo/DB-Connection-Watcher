<?php

namespace DBConnectionWatcher\Configuration;

class MissingOrExtraConfigurationsException extends \Exception
{
    const MESSAGE = "Missing (or extra) configuration(s) in '%1' section.";

    /**
     * MissingOrExtraConfigurationsException constructor.
     *
     * @param String $section The section when the exception has occurred.
     */
    public function __construct($section)
    {
        $message = str_replace('%1', $section, self::MESSAGE);
        parent::__construct($message);
    }
}
