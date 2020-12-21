<?php
/**
 * @var \App\View\AppView $this
 * @var string $content
 */

foreach (explode("\n", $content) as $line) {
    if ($line) {
        echo '<p> ' . $line . "</p>\n";
    }
}
