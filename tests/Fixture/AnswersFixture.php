<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AnswersFixture
 */
class AnswersFixture extends TestFixture
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
                'project_id' => 1,
                'question_id' => 1,
                'answer' => 'Lorem ipsum dolor sit amet',
                'created' => '2022-06-11 08:48:59',
                'modified' => '2022-06-11 08:48:59',
            ],
        ];
        parent::init();
    }
}
