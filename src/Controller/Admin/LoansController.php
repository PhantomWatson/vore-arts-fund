<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Model\Entity\Article;
use App\Model\Entity\ArticleImage;
use App\Model\Entity\Project;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * Loans Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Article> paginate(\Cake\Datasource\RepositoryInterface|\Cake\Datasource\QueryInterface|string|null $object = null, array $settings = [])
 */
class LoansController extends AdminController
{
    public function beforeFilter(EventInterface $event): ?Response
    {
        $this->addControllerBreadcrumb();

        return parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $projects = TableRegistry::getTableLocator()
            ->get('Projects')
            ->find('loanRecipients')
            ->contain(['Users'])
            ->orderByDesc('Projects.loan_awarded_date')
            ->all();

        $totalAwarded = $projects->sumOf('amount_awarded');
        $balances = [];
        foreach ($projects as $project) {
            $balances[$project->id] = $project->amount_awarded - $project->getTotalRepaid();
        }

        $this->title('Loans');
        $this->set([
            'balances' => $balances,
            'projects' => $projects,
            'totalAwarded' => $totalAwarded,
        ]);
    }
}
