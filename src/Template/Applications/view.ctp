<?php


use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

$this->layout = false;


$application = TableRegistry::getTableLocator()->get('Applications')->find()->where(['id' => $this->request->getParam('id') ])->all()->toArray();




