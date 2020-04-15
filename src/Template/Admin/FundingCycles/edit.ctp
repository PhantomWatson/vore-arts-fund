<?php
use Cake\ORM\TableRegistry;

$cakeDescription = 'CakePHP: the rapid development PHP framework';
$fundingCycle = TableRegistry::getTableLocator()->get('FundingCycles')->find()->where(['id'=>$this->request->getParam('id')])->all()->toArray();

?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->element('head'); ?>
    <title>
        <?= $cakeDescription ?>
    </title>
</head>

<body class="home">
    <?= $this->element('navbar'); ?>
    <div class="container">

        <div class='pb-2 mt-4 mb-2 border-bottom'>
            <h1>Edit Funding Cycle</h1>
        </div>
        <form>
            
        </form>
    </div>

</body>

</html>