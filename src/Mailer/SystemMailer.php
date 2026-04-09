<?php

namespace App\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Mailer;

class SystemMailer extends Mailer
{
    public function databaseBackupError(string $msg): void
    {
        $this
            ->setEmailFormat('both')
            ->setFrom(['noreply@voreartsfund.org' => 'Vore Arts Fund'])
            ->setSubject('Vore Arts Fund database backup failed (' . getEnvironment() . ')')
            ->setTo(Configure::read('supportEmail'))
            ->setViewVars(['content' => $msg]);
        $this->viewBuilder()->setTemplate('default');
    }
}
