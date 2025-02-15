<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bio $bio
 */
?>

<fieldset>
    <?= $this->Form->create($bio) ?>
    <?= $this->Form->control('title') ?>

    <div class="form-group text required">
        <label for="bio">Bio</label>
        <p>
            Start your bio with your name and write a third-person summary of your background and role in the community.
            HTML is allowed.
        </p>
        <?= $this->Form->textarea('bio') ?>
    </div>

    <?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
    <?= $this->Form->end() ?>
</fieldset>

