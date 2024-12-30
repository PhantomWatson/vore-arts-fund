<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Event\MailListener;
use App\Model\Entity\Note;
use App\Model\Entity\Project;
use App\Model\Table\NotesTable;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * ProjectsController
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @property \App\Model\Table\CategoriesTable $Categories
 * @property \App\Model\Table\ImagesTable $Images
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */

class ProjectsController extends AdminController
{
    /** @var bool Helps keep track of whether a "message sent" message should be shown */
    private $messageSent = false;

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
     * Page for reviewing a project
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

        $transactionsTable = $this->getTableLocator()->get('Transactions');
        $transactions = $transactionsTable->find('forProject', ['project_id' => $project->id]);


        /** @var NotesTable $notesTable */
        $notesTable = $this->fetchTable('Notes');
        if (!$this->request->is('get')) {
            // Assume save was successful unless if an error is encountered
            $successfullySaved = true;

            $this->messageSent = false;

            $data = $this->request->getData();

            $noteBody = $data['body'] ?? null;

            // Updating status
            if ($data['status_id'] ?? false) {
                $project = $this->Projects->patchEntity($project, ['status_id' => (int)$data['status_id']]);
                if ($this->Projects->save($project)) {
                    $this->Flash->success('Status updated');
                    $this->dispatchStatusChangeEvent($project, $noteBody);
                } else {
                    $this->Flash->error('Error updating status: ' . $this->getEntityErrorDetails($project));
                    $successfullySaved = false;
                }
            }

            // Adding note / sending message
            if ($noteBody) {
                $user = $this->getAuthUser();
                $data['user_id'] = $user?->id;
                $data['project_id'] = $projectId;
                $note = $notesTable->newEntity($data);
                if ($notesTable->save($note)) {
                    if ($note->type == Note::TYPE_MESSAGE) {
                        $this->dispatchMessageSentEvent($project, $note->body);
                        $this->messageSent = true;
                    } elseif (!$this->messageSent) {
                        $this->Flash->success('Note added');
                    }
                } else {
                    $this->Flash->error('Error adding note. Details: ' . print_r($note->getErrors(), true));
                    $successfullySaved = false;
                }
            }

            if ($this->messageSent) {
                $this->Flash->success('Message sent to applicant');
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
            'validStatusIds',
            'transactions',
        ));
        $this->title('Project: ' . $project->title);
        $this->addBreadcrumb(
            $project->funding_cycle->name,
            [
                'prefix' => 'Admin',
                'controller' => 'Projects',
                'action' => 'index',
                $project->funding_cycle_id,
            ]
        );
        $this->setCurrentBreadcrumb($project->title);

        return null;
    }

    public function markAwarded($projectId)
    {
        // Activate event listener
        $mailListener = new MailListener();
        $this->getEventManager()->on($mailListener);

        $projectId = $this->request->getParam('id');
        $project = $this->Projects->get($projectId);
        if (!$project) {
            $this->Flash->error('Project not found');
            return $this->redirect(['action' => 'review', 'id' => $projectId]);
        }
        if ($project->status_id != Project::STATUS_ACCEPTED) {
            $statuses = Project::getStatuses();
            $this->Flash->error(
                'Can\'t mark project as awarded unless if its status is ' . $statuses[Project::STATUS_ACCEPTED]
                . '. Its status is currently ' . $statuses[$project->status_id] . '.'
            );
            return $this->redirect(['action' => 'review', 'id' => $projectId]);
        }

        if ($this->getRequest()->is('post')) {
            $data = $this->getRequest()->getData();
            if (!($data['amount_awarded'] ?? false)) {
                $this->Flash->error('Amount awarded is required.');
                return null;
            }

            $project = $this->Projects->patchEntity($project, [
                'status_id' => Project::STATUS_AWARDED_NOT_YET_DISBURSED,
                'amount_awarded' => $data['amount_awarded'],
            ]);
            if ($this->Projects->save($project)) {
                $this->Flash->success('Status updated');
                $this->dispatchStatusChangeEvent($project);
                return $this->redirect(['action' => 'review', 'id' => $projectId]);
            }
            $this->Flash->error('Error updating status: ' . $this->getEntityErrorDetails($project));
        }
        return null;
    }

    /**
     * Returns the note type corresponding to a status that a project is being changed to
     *
     * @param int|null $statusId
     * @return string
     */
    private function getNoteType($statusId): string
    {
        return match ((int) $statusId) {
            Project::STATUS_REVISION_REQUESTED => Note::TYPE_REVISION_REQUEST,
            Project::STATUS_REJECTED => Note::TYPE_REJECTION,
            default => Note::TYPE_NOTE,
        };
    }

    /**
     * @param Project $project
     * @param string|null $noteBody
     * @return void
     */
    private function dispatchStatusChangeEvent(Project $project, ?string $noteBody = null): void
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
            case Project::STATUS_AWARDED_NOT_YET_DISBURSED:
                $event = new Event('Project.funded', $this, compact('project'));
                break;
            default:
                return;
        }
        $this->getEventManager()->dispatch($event);
        $this->messageSent = true;
    }

    /**
     * @param Project $project
     * @param string $message
     * @return void
     */
    private function dispatchMessageSentEvent(Project $project, string $message): void
    {
        $event = new Event(
            'Note.messageSent',
            $this,
            [
                'project' => $project,
                'message' => $message,
            ]
        );
        $this->getEventManager()->dispatch($event);
    }
}
