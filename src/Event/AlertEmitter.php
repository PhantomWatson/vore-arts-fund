<?php

namespace App\Event;

use Cake\Event\Event;
use Cake\Event\EventManager;

class AlertEmitter
{
    public static function emitMessageSentEvent(string $email, string $subject, array $viewVars, string $template): void
    {
        EventManager::instance()->on(new AlertListener());
        EventManager::instance()->dispatch(new Event(
            'Mail.messageSentToApplicant',
            null,
            [
                'email' => $email,
                'subject' => $subject,
                'viewVars' => $viewVars,
                'template' => $template,
            ]
        ));
    }
}
