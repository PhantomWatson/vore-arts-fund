<?php
declare(strict_types=1);

namespace App\Command;

use App\Nudges\NudgeInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

abstract class SendNudgesAbstractCommand extends Command
{
    /**
     * Returns the fully qualified class name of the Nudge class to use
     *
     * @return class-string<NudgeInterface>
     */
    abstract protected function getNudgeClass(): string;

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
     * Sends nudges for projects
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $nudgeFullClassName = $this->getNudgeClass();
        $projects = $nudgeFullClassName::getProjects();

        // Extract the simple class name from the full class name (e.g., "VoteNudge" from "App\Nudges\VoteNudge")
        $nudgeType = substr($nudgeFullClassName, strrpos($nudgeFullClassName, '\\') + 1);

        $io->out(sprintf(
            'Found %s %s that need %s',
            $projects->count(),
            __n('project', 'projects', $projects->count()),
            $nudgeType
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
            $result = $nudgeFullClassName::send($project);
            if ($result !== true) {
                $io->error("- $result");
                continue;
            }

            $io->success("- Sent $nudgeType for project #{$project->id}");
        }

        $io->out('Done');
    }
}
