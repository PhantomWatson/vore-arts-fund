<?php

namespace App\Email;

use Cake\Core\Configure;

/**
 * MailConfig class to hold email configuration settings.
 *
 * This class is used to define the default email settings for the application,
 * including the sender's email address, name, and subject prefix.
 */
class MailConfig
{
    public string $fromEmail;
    public string $fromName = 'Vore Arts Fund';
    public string $subjectPrefix = 'Vore Arts Fund - ';

    public function __construct()
    {
        $this->fromEmail = Configure::read('noReplyEmail');
    }
}
