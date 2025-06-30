<?php
/**
 * @var \App\View\AppView $this
 * @var bool $showLink
 */
$url = 'https://us06web.zoom.us/j/84939440863?pwd=f9xRsa3WImQTRYBrBE5DHggbAb5mJd.1';
?>

<p>
    Members of the public who are interested in the Vore Arts Fund are welcome to join our virtual board meetings and observe. At the end of the meeting, the board will invite questions and comments from guests.
</p>

<p>
    For our schedule of meetings, refer to <a href="https://muncieevents.com/tag/2244-vore-arts-fund">Muncie Events</a>.
</p>

<?php if ($showLink): ?>
    <p>
        <a href="<?= $url ?>">
            <?= $url ?>
        </a>
    </p>
<?php else: ?>
    <?= $this->Form->postLink(
        'Get link to join meeting',
        [],
        ['class' => 'btn btn-primary']
    ) ?>
<?php endif; ?>
