<?php

namespace App\Nudges;

use App\Model\Entity\Project;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\ResultSet;

interface NudgeInterface
{
    /**
     * Fetches projects that need nudges
     *
     * @return ResultSet<Project>|null
     */
    public static function getProjects(): ResultSetInterface|null;

    /**
     * Sends nudge messages and records them in the database
     *
     * @param Project $project
     * @return bool|string
     */
    public static function send(Project $project): bool|string;
}
