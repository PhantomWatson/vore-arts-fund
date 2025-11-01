<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\VoteNudge;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class SendVoteNudgesCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser
            ->addOption('dry-run', [
                'help' => 'Collect the nudges that would be sent, but don\'t actually send them.',
                'boolean' => true
            ]);

        return $parser;
    }

    /**
     * Creates a DB backup and emails it to the support email address
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $projects = VoteNudge::getProjects();

        $io->out(sprintf(
            'Found %s %s that need vote nudges',
            $projects->count(),
            __n('project', 'projects', $projects->count())
        ));

        if ($projects->isEmpty()) {
            $io->out('Done');
            return;
        }

        if ($args->getOption('dry-run')) {
            foreach ($projects as $project) {
                $io->out("- Would send nudge for project #{$project->id}");
            }
            $io->out('Done');
            return;
        }

        foreach ($projects as $project) {
            $result = VoteNudge::send($project);
            if ($result !== true) {
                $io->error("- $result");
                continue;
            }

            $io->success("- Sent vote nudge for project #{$project->id}");
        }

        $io->out('Done');
    }
}
