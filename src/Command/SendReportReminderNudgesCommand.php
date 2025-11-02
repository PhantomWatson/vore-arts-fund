<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\ReportReminderNudge;

/**
 * Command to send report-reminder nudges
 *
 * This should be scheduled later in the day than SendReportDueNudgesCommand
 */
class SendReportReminderNudgesCommand extends SendNudgesAbstractCommand
{
    /**
     * Returns the fully qualified class name of the Nudge class to use
     *
     * @return class-string<\App\Nudges\NudgeInterface>
     */
    protected function getNudgeClass(): string
    {
        return ReportReminderNudge::class;
    }
}
