<p>
    Donations and loan repayments by check are quite welcome and avoid the processing fees required for electronic payments.
</p>

<section class="section__with-spacer">
    <h2>Mail checks to</h2>
    <p>
        Vore Arts Fund<br />
        c/o The Art Mart<br />
        409 N. Martin St.<br />
        Muncie, Indiana, 47303
    </p>
</section>

<section class="section__with-spacer">
    <h2>
        Make checks payable to
    </h2>
    <p>
        Vore Arts Fund, Inc.
    </p>
</section>

<section class="section__with-spacer">
    <h2>If repaying a loan</h2>
    <p>
        Please include your project title and loan number in the memo line of the check. Your loan number can be found
        by going to
        <?= $this->Html->link(
            'My Loans',
            ['prefix' => 'My', 'controller' => 'Loans', 'action' => 'index'],
        ) ?>
        and viewing the details of your loan.
    </p>
</section>
