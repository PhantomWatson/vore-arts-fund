<?php

namespace App\Nudges;

use App\Alert\ErrorAlert;
use App\Email\MailConfig;
use App\Event\AlertEmitter;
use App\Model\Entity\Nudge;
use App\Model\Entity\Project;
use App\Model\Table\NudgesTable;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\TableRegistry;
use EmailQueue\EmailQueue;

/**
 * Sends nudges to applicants to alert them to a voting period beginning
 *
 * This strictly sends to applicants and not the full set of users because it's classified as a transactional email,
 * rather than a marketing one.
 */
class VoteNudge implements NudgeInterface
{
    /**
     * Projects that are approved and in the funding cycle whose voting period begins today
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface
    {
        $fundingCyclesTable = TableRegistry::getTableLocator()->get('FundingCycles');
        $fundingCycle = $fundingCyclesTable
            ->find('votingBeganToday')
            ->select(['id'])
            ->first();
        $fundingCycleId = $fundingCycle?->id;

        $projectsTable = TableRegistry::getTableLocator()->get('Projects');
        return $projectsTable
            ->find('eligibleForVoting', ['funding_cycle_id' => $fundingCycleId ?: 0])
            ->find('withoutNudge', ['nudgeType' => [Nudge::TYPE_VOTE_START]])
            ->where(['funding_cycle_id' => $fundingCycleId])
            ->contain(['FundingCycles'])
            ->all();
    }

    public static function send(Project $project): bool|string
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $mailConfig = new MailConfig();

        try {
            $user = $usersTable->get($project->user_id);
            $viewVars = [
                'projectTitle' => $project->title,
                'userName' => $user->name,
                'deadline' => $project->funding_cycle->vote_end_local->format('F jS'),
            ];
            $mailOptions = [
                'subject' => $mailConfig->subjectPrefix . 'Voting has begun!',
                'template' => 'nudges/vote_reminder',
                'from_name' => $mailConfig->fromName,
                'from_email' => $mailConfig->fromEmail,
            ];
            EmailQueue::enqueue($user->email, $viewVars, $mailOptions);
            AlertEmitter::emitMessageSentEvent($user->email, $mailOptions['subject'], $viewVars, $mailOptions['template']);
        } catch (\Exception $e) {
            $msg = "Error processing voting nudge for project #{$project->id}: " . $e->getMessage();
            (new ErrorAlert())->send($msg);
            return $msg;
        }

        /** @var NudgesTable $nudgesTable */
        $nudgesTable = TableRegistry::getTableLocator()->get('Nudges');
        $nudgesTable->addNudge($user->id, $project->id, Nudge::TYPE_VOTE_START);
        return true;
    }
}
