<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Bio $bio
 * @var string $rteJsPath
 * @var string $rteCssPath
 */
if ($rteCssPath) {
    $this->Html->css($rteCssPath, ['block' => true]);
}
if ($rteJsPath) {
    $this->Html->script($rteJsPath, ['block' => true, 'type' => 'module']);
}
?>

<?= $this->Form->create($bio, ['enctype' => 'multipart/form-data']) ?>
<fieldset>
    <?= $this->Form->control('title') ?>

    <div class="form-group text required">
        <label for="bio">Bio</label>
        <p>
            Start your bio with your name and write a third-person summary of your background and role in the community.
            HTML is allowed.
        </p>
        <?= $this->Form->textarea('bio', ['data-rte-target' => 1]) ?>
        <div id="rte-root"></div>
    </div>
</fieldset>

<fieldset class="upload-headshot">
    <legend>
        <?= ($bio->isNew() || !$bio->image) ? 'Upload a Headshot' : 'Update Headshot' ?>
    </legend>
    <div class="row">
        <div class="col-8">
            <div class="image-upload__input">
                <input type="file" class="form-control" aria-label="Select an image to upload" name="image-file" />
            </div>
        </div>
        <div class="col-4">
            <?php if ($bio->image) : ?>
                <?= $this->Html->image('/img/bios/' . $bio->user_id . '/' . $bio->image, ['alt' => 'Headshot']) ?>
            <?php endif; ?>
        </div>
    </div>
</fieldset>

<?= $this->Form->submit('Submit', ['class' => 'btn btn-primary']) ?>
<?= $this->Form->end() ?>

