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
            <h1>Welcome to the Vore Arts Fund</h1>
        </div>
        <div class="homepage">
            <div class="info">
                <p>
                    The Vore Arts Fund is a 501(c)(3) not-for-profit program for supporting the Muncie arts community
                    by distributing no-contract, no-interest loans to cover the up-front costs of producing commercial
                    art, music, performances, and educational opportunities.
                </p>
                <p>
                    We're currently fundraising and setting up the program's website, and applications for funding are
                    not yet open to the public. Stay tuned for updates.
                </p>
                <p>
                    Please email <a href="mailto:info@voreartsfund.org">info@voreartsfund.org</a> if you have any
                    questions.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
