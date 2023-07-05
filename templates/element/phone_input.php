<div class="input-with-footnote">
    <?= $this->Form->control('phone', [
        'type' => 'tel',
        'minLength' => 10,
        'label' => 'Phone number',
        'placeholder' => 'Cell number with area code, e.g. 765-123-4567'
    ]) ?>
    <p class="footnote">
        We only use this to verify your account and prevent multi-account abuse. Submitting your phone number
        is optional, but required if you wish to vote on applications. By submitting your phone number, you
        consent to receive a verification code via text message.
    </p>
</div>
