<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\PaymentReminderNudge;

class SendPaymentReminderNudgesCommand extends SendNudgesAbstractCommand
{
    /**
     * Returns the fully qualified class name of the Nudge class to use
     *
     * @return class-string<\App\Nudges\NudgeInterface>
     */
    protected function getNudgeClass(): string
    {
        return PaymentReminderNudge::class;
    }
}
