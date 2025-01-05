<?php
/**
 * @var \App\Model\Entity\Project $project
 * * @var \App\Model\Entity\FundingCycle $fundingCycle
 * * @var \App\View\AppView $this
 * * @var string $userName
 * * @var string $loanAgreementUrl
 * * @var string $replyUrl
 */
?>
Congratulations, <?= $userName ?>!

We've tallied the votes for the <?= $fundingCycle->name ?> funding cycle, and we're thrilled to tell you that the community ranked your application for funding for <?= $project->title ?> high enough that it was selected to receive a <?= $project->amount_awarded_formatted ?> loan!

The last step before we can mail your check is for you to read and agree to the terms of the loan agreement: <?= $loanAgreementUrl ?>.

Please do this as soon as you can so we can send you your payment without delay. If you have any questions, please visit <?= $replyUrl ?> to send us a message.
