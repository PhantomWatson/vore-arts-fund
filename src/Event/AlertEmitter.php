<?php

namespace App\Event;

use Cake\Event\Event;
use Cake\Event\EventManager;

class AlertEmitter
{
    public static function emitMessageSentEvent(string $email, string $subject, array $viewVars, string $template): void
    {
        $eventManager = EventManager::instance();

        // Only register the listener if it hasn't been registered yet
        if (!AlertListener::hasAlertListener($eventManager, 'Mail.messageSentToApplicant')) {
            $eventManager->on(new AlertListener());
        }

        $eventManager->dispatch(new Event(
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
