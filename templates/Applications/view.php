<?php
/**
 * @var \App\Model\Entity\Application $application
 * @var \App\Model\Entity\Category $category
 * @var \App\Model\Entity\Image $image
 * @var \App\View\AppView $this
 */
use Cake\ORM\TableRegistry;

$application = TableRegistry::getTableLocator()->get('Applications')->get($this->request->getParam('id'));
$category = TableRegistry::getTableLocator()->get('Categories')->find()->all()->toArray();
$image = TableRegistry::getTableLocator()
    ->get('Images')
    ->find()
    ->where(['application_id' => $application['id']])
    ->first();
?>
<div class='pb-2 mt-4 mb-2 border-bottom'>
    <h1><?= $application['title'] ?></h1>
</div>
<div>
    <h4>Description:</h4>
    <p><?= $application['description'] ?></p>
</div>
<div>
    <h4>Category:</h4>
    <p><?= $category[$application['category_id'] - 1]['name'] ?></p>
</div>
<div>
    <h4>Images:</h4>
    <?php
    if (isset($image) && !empty($image)) {
        echo $this->Html->image($image->path, ['alt' => $image->caption, 'height' => '200px', 'width' => '200px']);
    }
    ?>
    <p>Caption: <?= $image->caption ?></p>
</div>
<?= $this->Html->link(
    'Back',
    [
        'controller' => 'Votes',
        'action' => 'index',
    ],
    ['class' => 'btn btn-secondary']
) ?>
