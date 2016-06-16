<?php

namespace DBConnectionWatcher\DB;

class PreparedStatementCreationException extends \Exception
{
    const MESSAGE = 'An error occurred when creating the prepared statement for the query: ';

    /**
     * PreparedStatementCreationException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE . error_get_last()['message']);
    }
}
