<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use App\Model\Entity\Project;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProjectsFixture
 */
class ProjectsFixture extends TestFixture
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
                'user_id' => 1,
                'title' => 'Lorem ipsum dolor sit amet',
                'category_id' => 1,
                'description' => 'Lorem ipsum dolor sit amet',
                'amount_requested' => 1,
                'accept_partial_payout' => 1,
                'funding_cycle_id' => 1,
                'status_id' => Project::STATUS_UNDER_REVIEW,
                'address' => '123 Main St',
                'zipcode' => '47306',
                'loan_agreement_signature' => '',
                'created' => '2019-11-06 00:00:00',
                'modified' => '2019-11-06 00:00:00',
            ],
        ];
        parent::init();
    }
}
