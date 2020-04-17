<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Http\Exception\NotFoundException;

$this->layout = false;
echo $this->Html->css('styles');


if (!Configure::read('debug')) :
    throw new NotFoundException(
        'Please replace src/Template/Pages/home.ctp with your own version or re-enable debug mode.'
    );
endif;

$cakeDescription = 'CakePHP: the rapid development PHP framework';
?>
<!DOCTYPE html>
<html>

<head>
    <?= $this->element('head'); ?>
    <title>
        <?= $cakeDescription ?>
    </title>
</head>

<body class='home'>

    <?= $this->element('navbar'); ?>
    <div class='container'>
        <div class='pb-2 mt-4 mb-2 border-bottom'>
            <h1>Welcome to the Vore Arts Fund! </h1>
        </div>
        <div class = "homepage">
            <div class = "info">
                <h4>The Vore Arts Fund is a non-profit project funding profitable artistic projects 
                    in the Muncie community through no-contract, no-interest loans. The importance
                    of art in the community cannot be underestimated. We want to encourage and fund
                    artisitc projects and foster an environment that stresses the necessity of the arts.</h4>
                <h4>Register and apply now! </h4>
            </div>
            <div class = "images">
                <img src="/img/artmuseum.jpg" height="290" width="290" style=" border-radius: 8px;">
                <img src="/img/monet.jpg" height="300" width="300" style=" border-radius: 8px;">
                <img src="/img/love.jpg" height="292" width="292" style=" border-radius: 8px;">
            </div>
        </div>
    </div>
</body>

</html>