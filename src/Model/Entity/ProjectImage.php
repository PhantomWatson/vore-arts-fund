<?php

namespace App\Model\Entity;

/**
 * @property \App\Model\Entity\Project $project
 */
class ProjectImage extends Image
{
    protected array $_accessible = [
        'project_id' => true,
        'filename' => true,
        'weight' => true,
        'caption' => true,
        'created' => true,
        'project' => true,
    ];
}
