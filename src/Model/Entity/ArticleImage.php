<?php

namespace App\Model\Entity;

/**
 * @property int $article_id
 * @property \App\Model\Entity\Article $article
 */
class ArticleImage extends Image
{
    protected array $_accessible = [
        'article_id' => true,
        'filename' => true,
        'weight' => true,
        'caption' => true,
        'created' => true,
        'article' => true,
    ];
}
