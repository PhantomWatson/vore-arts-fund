<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\ErrorAlert;
use App\Model\Entity\Project;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;

/**
 * Scans votes created in the last 24 hours for data integrity violations
 */
class VoteDataIntegrityCommand extends Command
{
    /**
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $since = FrozenTime::now()->subHours(24);
        $votesTable = TableRegistry::getTableLocator()->get('Votes');
        $votes = $votesTable
            ->find()
            ->contain(['FundingCycles', 'Projects'])
            ->where(['Votes.created >=' => $since])
            ->all();

        // Check: project must belong to the vote's funding cycle
        $io->out('Checking that each vote\'s project belongs to the vote\'s funding cycle...');
        foreach ($votes as $vote) {
            if ($vote->project->funding_cycle_id !== $vote->funding_cycle_id) {
                $msg = "Vote #$vote->id: Project #$vote->project_id belongs to funding cycle #" . $vote->project->funding_cycle_id . " but vote is for funding cycle #$vote->funding_cycle_id";
                $io->error("- $msg");
                ErrorAlert::send($msg);
            }
        }
        $io->out('- Done', 2);

        // Check: project status must be one of the four valid voting statuses
        $io->out('Checking that each vote\'s project has a valid status...');
        $validStatuses = [
            Project::STATUS_ACCEPTED,
            Project::STATUS_AWARDED_AND_DISBURSED,
            Project::STATUS_AWARDED_NOT_YET_DISBURSED,
            Project::STATUS_NOT_AWARDED,
        ];
        foreach ($votes as $vote) {
            if (!in_array($vote->project->status_id, $validStatuses)) {
                $statusName = Project::getStatus($vote->project->status_id);
                $msg = "Vote #$vote->id: Project #$vote->project_id has invalid status for voting: $statusName";
                $io->error("- $msg");
                ErrorAlert::send($msg);
            }
        }
        $io->out('- Done', 2);

        // Check: vote's created date must be within the funding cycle's vote window
        $io->out('Checking that each vote was created within its funding cycle\'s voting window...');
        foreach ($votes as $vote) {
            $voteBegin = $vote->funding_cycle->vote_begin;
            $voteEnd = $vote->funding_cycle->vote_end;
            $created = $vote->created;

            if ($voteBegin === null || $voteEnd === null) {
                $msg = "Vote #$vote->id: Funding cycle #$vote->funding_cycle_id has no voting window defined";
                $io->error("- $msg");
                ErrorAlert::send($msg);
                continue;
            }

            if ($created < $voteBegin || $created > $voteEnd) {
                $msg = "Vote #$vote->id: Cast at $created, but funding cycle #$vote->funding_cycle_id's voting window is $voteBegin to $voteEnd";
                $io->error("- $msg");
                ErrorAlert::send($msg);
            }
        }
        $io->out('- Done', 2);
    }
}
