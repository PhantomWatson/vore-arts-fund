<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\VoteNudge;

class SendVoteNudgesCommand extends SendNudgesAbstractCommand
{
    /**
     * Returns the fully qualified class name of the Nudge class to use
     *
     * @return class-string<\App\Nudges\NudgeInterface>
     */
    protected function getNudgeClass(): string
    {
        return VoteNudge::class;
    }
}
