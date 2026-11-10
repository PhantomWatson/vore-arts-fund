<?php

namespace App\Mailer;

use App\Email\MailConfig;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;

class DonationMailer extends Mailer
{
    public function acknowledgement(string $email, int $transactionId): void
    {
        $transaction = TableRegistry::getTableLocator()->get('Transactions')->get($transactionId);
        $mailConfig = new MailConfig();
        $this
            ->setEmailFormat('both')
            ->setFrom([$mailConfig->fromEmail => $mailConfig->fromName])
            ->setSubject($mailConfig->subjectPrefix . 'Thank you for your donation!')
            ->setTo($email)
            ->setViewVars([
                'amount' => $transaction->dollar_amount_gross_formatted,
                'date' => $transaction->date_formatted,
                'donorName' => $transaction->name,
                'supportEmail' => Configure::read('supportEmail'),
            ]);
        $this->viewBuilder()->setTemplate('donation_acknowledgement');
    }
}
