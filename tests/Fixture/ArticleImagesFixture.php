<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArticleImagesFixture
 */
class ArticleImagesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'article_id' => 1,
                'filename' => 'article.jpg',
                'weight' => 1,
                'caption' => 'Lorem ipsum dolor sit amet',
                'created' => '2025-04-27 03:53:09',
            ],
        ];
        parent::init();
    }
}
