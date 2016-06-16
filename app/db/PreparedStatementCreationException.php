<?php

namespace DBConnectionWatcher\DB;

class PreparedStatementCreationException extends \Exception
{
    const MESSAGE = 'An error occurred when creating the prepared statement for the query: ';

    /**
     * PreparedStatementCreationException constructor.
     *
     * @param string $pgError PostgreSQL error string.
     */
    public function __construct($pgError)
    {
        parent::__construct(self::MESSAGE . $pgError);
    }
}
