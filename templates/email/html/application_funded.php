<?php
/**
 * @var \App\Model\Entity\Project $project
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var string $userName
 * @var string $loanAgreementUrl
 * @var string $replyUrl
 */
?>
<p>
    Congratulations, <?= $userName ?>!
</p>

<p>
    We've tallied the votes for the <?= $fundingCycle->name ?> funding cycle, and
    we're thrilled to tell you that the community ranked your application for funding for
    <strong><?= $project->title ?></strong> high enough that it was selected to receive a
    <?= $project->amount_awarded_formatted ?> loan!
</p>

<p>
    The last step before we can mail your check is for you to <a href="<?= $loanAgreementUrl ?>">read and agree to the terms of the loan agreement</a>.
</p>

<p>
    Please do this as soon as you can so we can send you your payment without delay. If you have any questions, please visit <a href="<?= $replyUrl ?>">the Messages page for this project</a> to send us a message.
</p>
