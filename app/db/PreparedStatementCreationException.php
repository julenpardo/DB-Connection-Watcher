<?php

namespace DBConnectionWatcher\DB;

class PreparedStatementCreationException extends \Exception
{
    const MESSAGE = 'An error occurred when creating the prepared statement for the query: ';

    /**
     * PreparedStatementCreationException constructor.
     *
     * @param string $errorMessage DBMS error message.
     */
    public function __construct($errorMessage = '')
    {
        parent::__construct(self::MESSAGE . $errorMessage);
    }
}
