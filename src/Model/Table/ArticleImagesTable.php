<?php

namespace App\Model\Table;

/**
 * @property \App\Model\Table\ArticlesTable&\Cake\ORM\Association\BelongsTo $Articles
 */
class ArticleImagesTable extends ImagesTable
{
    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('article_images');

        $this->belongsTo('Articles');
    }
}
