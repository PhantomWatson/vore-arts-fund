<div class="input-with-footnote">
    <?= $this->Form->control('phone', [
        'type' => 'tel',
        'minLength' => 10,
        'label' => 'Phone number',
        'placeholder' => 'Cell number with area code, e.g. 765-123-4567'
    ]) ?>
    <p class="footnote">
        <strong>This is optional</strong>, but a verified phone number is required in order to vote on applications.
        We only use this your phone number to verify your account and prevent multi-account abuse.
        By submitting your phone number, you consent to receive a verification code via text message.
    </p>
</div>
