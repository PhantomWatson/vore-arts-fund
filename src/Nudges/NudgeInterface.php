<?php

namespace App\Nudges;

use App\Model\Entity\Project;
use Cake\Collection\CollectionInterface;

interface NudgeInterface
{
    /**
     * Fetches projects that need nudges
     *
     * @return CollectionInterface<Project>|null
     */
    public static function getProjects(): ?CollectionInterface;

    /**
     * Sends nudge messages and records them in the database
     *
     * @param Project $project
     * @return bool|string
     */
    public static function send(Project $project): bool|string;
}
