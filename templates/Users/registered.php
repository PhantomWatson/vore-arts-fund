<?php
/**
 * @var \App\View\AppView $this
 * @var bool $shouldVerifyPhone
 * @var bool $verificationCodeSent
 * @var string $verificationCodeSentMsg
 */
?>
<p class="alert alert-success">
    Congratulations! Your Vore Arts Fund account has been registered, and you've been automatically logged in.
</p>

<?php if ($shouldVerifyPhone): ?>
    <?php if ($verificationCodeSent): ?>
        <p>
            <?= $verificationCodeSentMsg ?>
            Once you receive it, you can enter it on the
            <?= $this->Html->link(
                'Account > Verify phone number',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'verify',
                ]
            ) ?>
            page to unlock your ability to vote on funding applications.
        </p>
    <?php else: ?>
        <p>
            To unlock your ability to vote on funding applications, visit the
            <?= $this->Html->link(
                'Account > Verify phone number',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'verify',
                ]
            ) ?>
            page to receive a verification code.
        </p>
    <?php endif; ?>
<?php endif; ?>

<p>
    Next, you can
</p>
<ul>
    <li>
        <?= $this->Html->link(
            'check for current and upcoming funding cycles',
            [
                'prefix' => false,
                'controller' => 'FundingCycles',
                'action' => 'index',
            ],
        ) ?>,
    </li>
    <li>
        <?= $this->Html->link(
            'apply for funding',
            [
                'prefix' => false,
                'controller' => 'Projects',
                'action' => 'apply',
            ],
        ) ?>,
    </li>
    <li>
        <?= $this->Html->link(
            'vote on applications',
            [
                'prefix' => false,
                'controller' => 'Votes',
                'action' => 'index',
            ],
        ) ?>,
    </li>
    <li>
        and
        <?= $this->Html->link(
            'donate',
            [
                'prefix' => false,
                'controller' => 'Donate',
                'action' => 'index',
            ],
        ) ?>
        to help support our mission to support the local arts.
    </li>
</ul>
