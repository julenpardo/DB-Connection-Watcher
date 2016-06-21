<?php

namespace DBConnectionWatcher\Mailer;

class MailSendException extends \Exception
{
    const MESSAGE = 'An error occur when sending the mail: ';

    /**
     * MailSendException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE . error_get_last()['message']);
    }
}
