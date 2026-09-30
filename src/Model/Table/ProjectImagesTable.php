<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;

/**
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 */
class ProjectImagesTable extends ImagesTable
{
    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['project_id'], 'Projects'));

        return $rules;
    }

    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('project_images');

        $this->belongsTo('Projects');
    }

    public function isOwnedBy(int $imageId, int $userId): bool
    {
        $image = $this->get($imageId);
        return $this->Projects->exists(['id' => $image->project_id, 'user_id' => $userId]);
    }
}
