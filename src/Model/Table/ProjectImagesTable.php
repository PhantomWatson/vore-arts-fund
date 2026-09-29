<?php

namespace App\Model\Table;

/**
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 */
class ProjectImagesTable extends ImagesTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('project_images');

        $this->belongsTo('Projects');
    }
}
