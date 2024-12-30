<?php

use App\Model\Entity\Project;

/**
 * @var Project $project
 */
?>

<p>
    By marking a project as awarded,
</p>
<ul>
    <li>
        an email will go out to the applicant congratulating them on being awarded a loan
    </li>
    <li>
        they'll also receive instruction on how to submit a loan agreement
    </li>
    <li>
        no payment should be sent out until the loan agreement has been submitted
    </li>
</ul>

<?= $this->Form->create($project) ?>
<?= $this->Form->control('amount_awarded') ?>
<?= $this->Form->submit('Submit', ['confirm' => 'Are you sure you want to designate this project for funding?']) ?>
<?= $this->Form->end() ?>
