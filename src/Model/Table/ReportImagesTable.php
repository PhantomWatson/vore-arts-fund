<?php

namespace App\Model\Table;

/**
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\BelongsTo $Reports
 */
class ReportImagesTable extends ImagesTable
{
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
}
