<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\ReportDueNudge;

/**
 * Command to send report-due nudges
 *
 * This should be scheduled earlier in the day than SendReportReminderNudgesCommand
 */
class SendReportDueNudgesCommand extends SendNudgesAbstractCommand
{
    /**
     * Returns the fully qualified class name of the Nudge class to use
     *
     * @return class-string<\App\Nudges\NudgeInterface>
     */
    protected function getNudgeClass(): string
    {
        return ReportDueNudge::class;
    }
}
