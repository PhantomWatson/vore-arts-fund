<?php
/**
 * @var \App\View\AppView $this
 */
?>

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <?= $this->Form->create(null, ['id' => 'login-form']) ?>
            <div class="modal-header">
                <h5 class="modal-title">Your session has expired. Please log in again.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" style="display: none">
                    Incorrect email or password
                </div>
                <?= $this->Form->control('email') ?>
                <?= $this->Form->control('password', ['value' => '']) ?>
                <div class="form-check">
                    <label for="stay-logged-in" class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="remember_me" value="1" id="stay-logged-in" />
                        Stay logged in
                    </label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?= $this->Form->submit(__('Log in'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
