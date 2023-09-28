<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Event\MailListener;
use App\Model\Entity\Project;
use App\Model\Entity\User;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * FundingCyclesController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ProjectsController extends AdminController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->addControllerBreadcrumb();
    }

    /**
     * Projects index page
     *
     * @return void
     */
    public function index($fundingCycleId = null)
    {
        $this->title('Projects');
        $fundingCyclesTable = $this->fetchTable('FundingCycles');
        $projects = [];

        if (!$fundingCycleId) {
            $currentCycle = $fundingCyclesTable->find('current')->first();
            $fundingCycleId = $currentCycle ? $currentCycle->id : null;
        }

        if ($fundingCycleId) {
            $projects = $this
                ->Projects
                ->find()
                ->where(['funding_cycle_id' => $fundingCycleId])
                ->all();
        }

        $this->set([
            'projects' => $projects,
            'fundingCycles' => $fundingCyclesTable->find()->orderDesc('application_begin')->all(),
            'fundingCycleId' => $fundingCycleId,
        ]);
    }

    /**
     * Page for reviewing an project
     *
     * @return Response|null
     */
    public function review()
    {
        // Activate event listener
        $mailListener = new MailListener();
        $this->getEventManager()->on($mailListener);

        $projectId = $this->request->getParam('id');
        $project = $this->Projects->getForViewing($projectId);
        if (!$project) {
            $this->Flash->error('Project not found');
            return $this->redirect(['action' => 'index']);
        }

        $notesTable = $this->fetchTable('Notes');
        if (!$this->request->is('get')) {
            // Assume save was successful unless if an error is encountered
            $successfullySaved = true;

            $data = $this->request->getData();

            $noteBody = $data['body'] ?? null;

            // Updating status
            if ($data['status_id'] ?? false) {
                $project->status_id = (int)$data['status_id'];
                if ($this->Projects->save($project)) {
                    $this->Flash->success('Status updated');
                    $this->dispatchStatusChangeEvent($project, $noteBody);
                } else {
                    $this->Flash->error('Error updating status');
                    $successfullySaved = false;
                }
            }

            // Adding note
            if ($noteBody) {
                $user = $this->getAuthUser();
                $data['user_id'] = $user?->id;
                $data['project_id'] = $projectId;
                $note = $notesTable->newEntity($data);
                if ($notesTable->save($note)) {
                    $this->Flash->success('Note added');
                } else {
                    $this->Flash->error('Error adding note. Details: ' . print_r($note->getErrors(), true));
                    $successfullySaved = false;
                }
            }

            // POST/Redirect/GET pattern
            if ($successfullySaved) {
                return $this->redirect([
                    'action' => 'review',
                    'id' => $projectId,
                ]);
            }
        }

        $statusActions = Project::getStatusActions();
        $validStatusIds = Project::getValidStatusOptions($project->status_id);

        // Set view vars
        $questionsTable = $this->fetchTable('Questions');
        $questions = $questionsTable->find('forProject')->toArray();
        $notes = $notesTable
            ->find()
            ->where(['project_id' => $projectId])
            ->contain(['Users'])
            ->orderDesc('Notes.created')
            ->all();
        $newNote = $notesTable->newEmptyEntity();
        $toLoad = $this->getAppFiles('review');
        $this->set(compact(
            'statusActions',
            'project',
            'newNote',
            'notes',
            'questions',
            'toLoad',
            'validStatusIds'
        ));
        $this->title('Project: ' . $project->title);
        $this->setCurrentBreadcrumb($project->title);

        return null;
    }

    /**
     * @param Project $project
     * @param string|null $noteBody
     * @return void
     */
    private function dispatchStatusChangeEvent(Project $project, ?string $noteBody)
    {
        switch ($project->status_id) {
            case Project::STATUS_ACCEPTED:
                $event = new Event('Project.accepted', $this, compact('project'));
                break;
            case Project::STATUS_REVISION_REQUESTED:
                $event = new Event('Project.revisionRequested', $this, compact('project', 'noteBody'));
                break;
            case Project::STATUS_REJECTED:
                $event = new Event('Project.rejected', $this, compact('project', 'noteBody'));
                break;
            default;
                return;
        }
        $this->getEventManager()->dispatch($event);
    }
}
