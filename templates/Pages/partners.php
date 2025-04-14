<?php
/**
 * @var \App\View\AppView $this
 */
?>
<p>
    The Vore Arts Fund partners with local businesses to offer special discounts to funding recipients so that their money can go even further. If your business would like to join us and help local working artists succeed, <a href="/contact">reach out</a> and tell us about it!
</p>

<table class="table">
    <thead>
        <tr>
            <th>Partner</th>
            <th>Provides</th>
            <th>Discount</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <a href="https://www.artmartmuncie.net">The Art Mart</a>
        </td>
        <td>
            Supplies for painting, printing, drawing, model building, and sculpting
        </td>
        <td>
            20% discount
        </td>
    </tr>
    <tr>
        <td>
            <a href="https://www.gordyframing.com/">Gordy Fine Art and Framing</a>
        </td>
        <td>
            Custom framing, readymade frames, photo printing, high-resolution scanning, and curation consultation
        </td>
        <td>
            25% discount
        </td>
    </tr>
    <tr>
        <td>
            <a href="https://www.madjax.org">Madjax</a>
        </td>
        <td>
            Free membership, with access to three maker spaces and a wide variety of equipment
        </td>
        <td>
            Six months free
        </td>
    </tr>
    <tr>
        <td>
            <a href="https://www.tribune-showprint.com">Tribune Showprint</a>
        </td>
        <td>
            Letterpress poster printing, digital printing, and graphic design for a variety of printing needs
        </td>
        <td>
            20% discount
        </td>
    </tr>
    </tbody>
</table>

<p>
    If you're already a community partner, you can check our
    <?= $this->Html->link(
        'discount eligibility',
        [
            'controller' => 'Pages',
            'action' => 'discountEligibility',
        ]
    ) ?>
    page to verify customer eligibility.
</p>
