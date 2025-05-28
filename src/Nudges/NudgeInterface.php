<?php

namespace App\Nudges;

use App\Model\Entity\Project;
use Cake\Datasource\ResultSetInterface;

interface NudgeInterface
{
    /**
     * Fetches projects that need nudges
     *
     * @return ResultSetInterface|Project[]
     */
    public static function getProjects(): ResultSetInterface;

    /**
     * Sends nudge messages and records them in the database
     *
     * @param Project $project
     * @return bool|string
     */
    public static function send(Project $project): bool|string;
}
