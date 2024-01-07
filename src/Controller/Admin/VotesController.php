<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Event\MailListener;
use App\Model\Entity\Note;
use App\Model\Entity\Project;
use App\Model\Table\FundingCyclesTable;
use App\Model\Table\NotesTable;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * VotesController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class VotesController extends AdminController
{
    /** @var bool Helps keep track of whether a "message sent" message should be shown */
    private $messageSent = false;

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb();
    }

    /**
     * Voting results index page
     *
     * @return void
     */
    public function index($fundingCycleId = null)
    {
        /** @var FundingCyclesTable $fundingCyclesTable */
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $projectsTable = $this->fetchTable('Projects');

        if (!$fundingCycleId) {
            $currentCycle = $fundingCyclesTable->find('current')->first();
            $fundingCycleId = $currentCycle ? $currentCycle->id : null;
        }
        $fundingCycle = $fundingCycleId ? $fundingCyclesTable->get($fundingCycleId) : null;
        $this->title($fundingCycle ? $fundingCycle->name . ' voting results' : 'Voting results');

        /** @var Project[] $projects */
        $projects = $fundingCycleId
            ? $projectsTable
                ->find()
                ->where(['funding_cycle_id' => $fundingCycleId])
                ->contain(['Votes'])
                ->toArray()
            : [];

        usort($projects, function (Project $projectA, Project $projectB) {
            if ($projectA->voting_score == $projectB->voting_score) {
                return 0;
            }
            if ($projectA->voting_score === null) {
                return 1;
            }
            return ($projectA->voting_score < $projectB->voting_score) ? 1 : -1;
        });

        $this->set([
            'projects' => $projects,
            'fundingCycle' => $fundingCycle,
            'fundingCycles' => $fundingCyclesTable->find()->orderDesc('application_begin')->all(),
        ]);
    }
}
