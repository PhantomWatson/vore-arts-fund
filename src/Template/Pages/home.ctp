<?php
/**
 * @var \App\View\AppView $this
 */
$this->layout = false;
echo $this->Html->css('styles');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= $this->element('head') ?>
    <title>
        Vore Arts Fund
    </title>
</head>

<body class="home">

    <?= $this->element('navbar') ?>
    <div class="container">
        <div class="pb-2 mt-4 mb-2 border-bottom">
            <h1>Welcome to the Vore Arts Fund!</h1>
        </div>
        <div class="homepage">
            <div class = "info">
                <h4>The Vore Arts Fund is a non-profit project funding profitable artistic projects
                    in the Muncie community through no-contract, no-interest loans. The importance
                    of art in the community cannot be underestimated. We want to encourage and fund
                    artistic projects and foster an environment that stresses the necessity of the arts.</h4>
                <h4>Register and apply now!</h4>
            </div>
            <div class="images">
                <img src="/img/artmuseum.jpg" height="290" width="290" style=" border-radius: 8px;">
                <img src="/img/monet.jpg" height="300" width="300" style=" border-radius: 8px;">
                <img src="/img/love.jpg" height="292" width="292" style=" border-radius: 8px;">
            </div>
        </div>
    </div>
</body>

</html>
