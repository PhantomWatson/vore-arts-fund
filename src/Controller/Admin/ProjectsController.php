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

        if (!$this->request->is('get')) {
            $redirect = $this->processReview($project);
            if ($redirect) {
                return $redirect;
            }

            // Re-fetch to clear invalid changes
            $project = $this->Projects->getForViewing($projectId);
        }

        $statusActions = Project::getStatusActions();
        $validStatusIds = Project::getValidStatusOptions($project->status_id);

        // Set view vars
        $questionsTable = $this->fetchTable('Questions');
        $questions = $questionsTable->find('forProject')->toArray();
        /** @var NotesTable $notesTable */
        $notesTable = $this->fetchTable('Notes');
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
     * @return int
     */
    private function getNoteType($statusId): int
    {
        return match ((int) $statusId) {
            Project::STATUS_REVISION_REQUESTED => Note::TYPE_REVISION_REQUEST,
            Project::STATUS_REJECTED => Note::TYPE_REJECTION,
            default => Note::TYPE_NOTE,
        };
    }

    /**
     * @param Project $project
     * @param string|null $messageBody
     * @return void
     */
    private function dispatchStatusChangeEvent(Project $project, ?string $messageBody = null): void
    {
        switch ($project->status_id) {
            case Project::STATUS_ACCEPTED:
                $event = new Event('Project.accepted', $this, compact('project'));
                break;
            case Project::STATUS_REVISION_REQUESTED:
                $event = new Event('Project.revisionRequested', $this, compact('project', 'messageBody'));
                break;
            case Project::STATUS_REJECTED:
                $event = new Event('Project.rejected', $this, compact('project', 'messageBody'));
                break;
            case Project::STATUS_NOT_AWARDED:
                $event = new Event('Project.notFunded', $this, compact('project'));
                break;
            case Project::STATUS_AWARDED_NOT_YET_DISBURSED:
                $event = new Event('Project.funded', $this, compact('project'));
                break;
            default:
                return;
        }
        $this->getEventManager()->dispatch($event);
        $this->Flash->success('Message sent to applicant');
    }

    /**
     * @param Project $project
     * @return Response|null
     */
    private function processReview(Project $project): ?Response
    {
        $data = $this->request->getData();
        $messageBody = $this->request->getData('message');
        $statusId = (int)$this->request->getData('status_id');

        // Validate
        if (!$statusId) {
            $this->Flash->error('Error updating status: No new status selected');
            return null;
        }
        $validNewStatusIds = Project::getValidStatusOptions($project->status_id);
        if ($statusId != $project->status_id && !in_array($statusId, $validNewStatusIds)) {
            $this->Flash->error(
                'Can\'t change status from ' . Project::getStatus($project->status_id)
                . ' to ' . Project::getStatus($statusId)
            );
        }
        if (in_array($statusId, Project::getStatusesNeedingMessages())) {
            if (!$data['message'] ?? false) {
                $this->Flash->error('Message is required.');
                return null;
            }
        }
        if ($statusId == Project::STATUS_ACCEPTED && !$this->request->getData('amount_awarded')) {
            $this->Flash->error('Amount awarded is required.');
            return null;
        }

        // Update status
        $project->status_id = $statusId;
        if ($statusId == Project::STATUS_ACCEPTED) {
            $project = $this->Projects->patchEntity(
                $project,
                ['amount_awarded' => $this->getRequest()->getData('amount_awarded')]
            );
        }

        if ($this->Projects->save($project)) {
            $this->Flash->success('Status updated');
            $this->dispatchStatusChangeEvent($project, $messageBody);

            // Save note
            if (in_array($statusId, Project::getStatusesNeedingMessages())) {
                $this->saveNote($messageBody, $this->getNoteType($statusId), $project);
            }
        } else {
            $this->Flash->error('Error updating status: ' . $this->getEntityErrorDetails($project));
            return null;
        }

        // POST-redirect-GET pattern
        return $this->redirect([
            'action' => 'review',
            'id' => $project->id,
        ]);
    }

    private function saveNote(string $noteBody, int $noteType, Project $project): bool
    {
        $user = $this->getAuthUser();

        /** @var NotesTable $notesTable */
        $notesTable = $this->fetchTable('Notes');
        $message = $notesTable->newEntity([
            'type' => $noteType,
            'body' => $noteBody,
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        if ($notesTable->save($message)) {
            return true;
        }

        $this->Flash->error(
            'Error sending message'
            . 'Details: ' . $this->getEntityErrorDetails($message)
        );
        return false;
    }

    /**
     * Used for both private notes and messages to the applicant
     *
     * @return Response
     */
    public function newNote()
    {
        $this->processNewNote();
        return $this->redirect([
            'action' => 'review',
            'id' => $this->request->getParam('id'),
        ]);
    }

    /**
     * Used for both private notes and messages to the applicant
     *
     * @return bool
     */
    private function processNewNote(): bool
    {
        $projectId = $this->request->getParam('id');
        $project = $this->Projects->get($projectId);
        $noteBody = $this->request->getData('body');
        $type = $this->request->getData('type');
        if (!$noteBody) {
            $this->Flash->error('Message body is required.');
            return false;
        }
        if (!in_array($type, [Note::TYPE_NOTE, Note::TYPE_MESSAGE_TO_APPLICANT])) {
            $this->Flash->error('Invalid note type: ' . $type);
            return false;
        }

        if (!$this->saveNote($noteBody, $type, $project)) {
            $this->Flash->error('Error saving ' . ($type == Note::TYPE_NOTE ? 'note' : 'message'));
            return false;
        }

        if ($type != Note::TYPE_MESSAGE_TO_APPLICANT) {
            $this->Flash->success('Note saved');
            return true;
        }

        // Dispatch event to trigger email
        $mailListener = new MailListener();
        $this->getEventManager()->on($mailListener);
        $event = new Event(
            'Note.messageSent',
            $this,
            [
                'project' => $project,
                'message' => $noteBody,
            ]
        );
        $this->getEventManager()->dispatch($event);
        $this->Flash->success('Message sent to applicant');
        return true;
    }
}
