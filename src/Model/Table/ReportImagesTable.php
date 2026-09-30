<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;

/**
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\BelongsTo $Reports
 */
class ReportImagesTable extends ImagesTable
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
        $rules->add($rules->existsIn(['report_id'], 'Reports'));

        return $rules;
    }

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('report_images');

        $this->belongsTo('Reports');
    }

    public function isOwnedBy(int $imageId, int $userId): bool
    {
        $image = $this->get($imageId);
        return $this->Reports->exists(['id' => $image->report_id, 'user_id' => $userId]);
    }
}
