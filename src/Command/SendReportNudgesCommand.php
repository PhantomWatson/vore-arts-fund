<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\ReportNudge;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class SendReportNudgesCommand extends Command
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
        $projects = ReportNudge::getProjects();

        $io->out(sprintf(
            'Found %s %s that need report nudges',
            $projects->count(),
            __n('report', 'reports', $projects->count())
        ));

        if ($projects->isEmpty()) {
            $io->out('Done');
            return;
        }

        foreach ($projects as $project) {
            $result = ReportNudge::send($project);
            if ($result !== true) {
                $io->error("- $result");
                continue;
            }

            $io->success("- Sent report nudge for project #{$project->id}");
        }

        $io->out('Done');
    }
}
