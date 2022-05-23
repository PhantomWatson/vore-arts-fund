<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class AdminController extends AppController
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
