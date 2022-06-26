<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;

class DashboardController extends AdminController
{
    /**
     * Admin index page
     *
     * @return void
     */
    public function index()
    {
        $this->title('Admin');
        $this->set([
            'applications' => TableRegistry::getTableLocator()->get('Applications')->find()->all()->toArray(),
        ]);
    }
}
