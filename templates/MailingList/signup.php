<?php
/**
 * @var \App\View\AppView $this
 */
// Code pulled from https://vr2.verticalresponse.com/#/signup_forms/43980465123985/share
$this->Html->script('https://vr2.verticalresponse.com/signup_forms/signup_forms.embedded-2.js', ['block' => 'script']);
?>

<p>
    Sign up to our mailing list to stay up-to-date on new developments from the Vore Arts Fund, opportunities to support
    our mission, and announcements about applying for funding and voting on applications.
</p>

<form class="vr-signup-form" id="vr-signup-form-43980465123985">
    <div class="vr-field mb-3">
        <label for="signup-email" class="form-label">Email Address</label>
        <input type="email" name="email_address" required id="signup-email" class="form-control" />
    </div>
    <div class="vr-submit">
        <div class="vr-notice"></div>
        <button type="submit" class="btn btn-primary">
            Sign up
        </button>
    </div>
</form>

<script type="text/javascript">
    preventMultipleSubmit('#vr-signup-form-43980465123985');

    if (typeof VR !== "undefined" && typeof VR.SignupForm !== "undefined") {
        new VR.SignupForm({
            id: "43980465123985",
            element: "vr-signup-form-43980465123985",
            endpoint: "https://vr2.verticalresponse.com/se/",
            embeddable: "true",
            redirect_url: <?= json_encode(\Cake\Routing\Router::url([
                'controller' => 'MailingList',
                'action' => 'thanks'
            ], true)) ?>,
            submitLabel: "Submitting...",
            invalidEmailMessage: "Invalid email address",
            generalErrorMessage: "An error occurred",
            notFoundMessage: "Sign up form not found",
            successMessage: "Success! Please check your inbox (and spam folder) for a confirmation email.",
            nonMailableMessage: "Nonmailable address"
        });
    }
</script>
