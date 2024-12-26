<?php
/**
 * @var string $amount
 * @var string $dueDate
 * @var int $version
 */

use App\Model\Entity\Project;

// Defaults (for a non-signable example agreement)
$amount = $amount ?? '[LOAN AMOUNT]';
$dueDate = $dueDate ?? '[LOAN DUE DATE]';
$version = $version ?? Project::getLatestTermsVersion();

$terms = include(APP . DS . 'LoanTerms' . DS . 'loan_terms_' . $version . '.php');
?>

<ol class="loan-agreement">
    <?php foreach ($terms as $section => $sectionTerms): ?>
        <li>
            <?= $section ?>
            <ol type="a">
                <?php foreach ($sectionTerms as $term): ?>
                    <li>
                        <?= str_contains($term, ':') ? '<strong>' . str_replace(':', ':</strong>', $term) : $term ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </li>
    <?php endforeach; ?>
</ol>
