<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[] $boardMembers
 */
?>

<div class="about">
    <article>
        <h2>
            What is the Vore Arts Fund?
        </h2>
        <p>
            The Vore Arts Fund is a 501(c)(3) not-for-profit corporation created to financially support the arts
            and entertainment community of Muncie, Indiana. It distributes
            <strong>
                zero-interest, unconditionally-forgivable loans to cover the up-front costs of producing for-profit art,
                music, performances, and art education.
            </strong>
        </p>
        <p>
            By absorbing the financial risk inherent in arts-related projects with up-front costs, this program makes it
            easier for artists, educators, performers, and promoters to improve the quality-of-place of our community.
            By distributing loans and targeting for-profit projects, this program can sustain itself without
            depending on an ongoing fundraising effort. In the future, we hope to grow it to also distribute grants to
            support noncommercial art endeavors, such as public murals, free performances, and free art classes.
        </p>
    </article>

    <article>
        <h2>
            Where did the name come from?
        </h2>
        <img src="/img/phil.jpg" alt="Photo of Phil Vore" title="Phil Vore" class="about-phil" />
        <p>
            <strong>Phil Vore</strong> served as a director on the boards of the Full Circle Arts Co-op and the Muncie Arts and Culture
            Council from 2007 until his death in 2017, and during that time he championed the idea that such nonprofits
            must prove their worth to the community by directly and materially supporting artists. Whenever our focus
            would stray too far from our real purpose, Phil would remind us that we needed to be spending our time
            identifying and fulfilling the needs of the artists who help make our community beautiful, interesting,
            entertaining, and enlightening, or else we're just wasting our own time and resources. It is in the memory
            of our friend Phil and the practical, straightforward, and meaningful approach to supporting the arts that
            he insisted upon that we have named this project.
        </p>
    </article>

    <article>
        <h2>
            Who's eligible?
        </h2>
        <p>
            To apply for funding, you must be a <strong>resident of Muncie, Indiana</strong>, at least 18, and seeking support for the
            up-front costs of an <strong>arts-related project</strong> that is expected to generate enough money to pay back those costs.
        </p>
        <p>
            For more details about who qualifies, refer to the <a href="/terms#eligibility">eligibility requirements</a>
            section of our Terms of Service page.
        </p>
    </article>

    <article>
        <h2>
            How are funding decisions made?
        </h2>
        <p>
            A review committee reviews all applications to make sure they meet our eligibility requirements, and any
            accepted applications are presented to the public to vote on, using ranked-choice voting. Once the voting period
            is over, the money budgeted for that funding cycle gets distributed to the highest-ranked application, followed
            by the second-highest, and so on until that cycle's budget is spent. In other words,
            <strong>the public determines our funding priorities</strong>.
        </p>
    </article>

    <article>
        <h2>
            What are the loan repayment terms?
        </h2>
        <p>
            <strong>No interest</strong> will ever be charged for any loans, but "voluntary interest" (think of it as
            adding a tip on top of your repayment) is very appreciated, since it helps us offset losses incurred from
            failed projects. Loans have no deadline for repayment, will never be reported to a credit agency, and will
            never be passed on to a third party such as a collection agency. We aim for the Vore Arts Fund to be
            <strong>as low-risk as possible</strong> for all participants.
        </p>
        <p>
            For more details about loan terms, refer to the <a href="/terms#loan-terms">loan terms</a> section of our
            Terms of Service page.
        </p>
    </article>

    <article>
        <h2>
            What if my funded project fails?
        </h2>
        <p>
            In the event a funded project fails to generate enough money to repay a loan, that loan can be
            <strong>unconditionally forgiven</strong>. The Vore Arts Fund accepts the risk of your project potentially
            failing, and that helps you, the artist, to focus on your work without worrying about whether it will
            bankrupt you.
        </p>
    </article>
</div>

<div class="about">
    <article>
        <h2>
            Community Partners
        </h2>
        <p>
            Local businesses have partnered with us to offer special discounts to our funding recipients so our support goes even further. For a full list of partners and discounts offered, visit
            <?= $this->Html->link(
                'our Partners page',
                [
                    'controller' => 'Pages',
                    'action' => 'partners',
                ]
            ) ?>.
        </p>
    </article>
</div>

<?php if ($boardMembers): ?>
    <section id="meet-the-board">
        <h2>
            Meet Our Board of Directors
        </h2>
        <div class="row">
            <?php foreach ($boardMembers as $boardMember): ?>
                <div class="col-lg-6">
                    <article class="card">
                        <div class="card-body">
                            <h3 class="card-title">
                                <span class="name">
                                    <?= $boardMember->name ?>
                                </span>
                                <span class="title">
                                    <?= ($boardMember->bio ?? false) ? $boardMember->bio->title : 'Director' ?>
                                </span>
                            </h3>
                            <div class="card-text">
                                <?php if ($boardMember->bio ?? false): ?>
                                    <?= $boardMember->bio->image
                                        ? $this->Html->image(
                                            $boardMember->bio->image_url,
                                            [
                                                'alt' => "Headshot of $boardMember->name",
                                                'class' => 'bio-headshot float-md-end mb-4 mb-md-2 ms-md-2',
                                            ]
                                        ) : null
                                    ?>
                                    <?= $boardMember->bio->formatted_bio ?>
                                <?php else: ?>
                                    <p>
                                        Bio coming soon!
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php else: ?>
    <div class="row">
        <div class="col-sm-6">
            <section class="credits">
                <h2>
                    Board of Directors
                </h2>
                <dl>
                    <div>
                        <dt>
                            President
                        </dt>
                        <dd>
                            Graham Watson <span class="pronoun">(he)</span>
                        </dd>
                    </div>
                    <div>
                        <dt>
                            Vice President
                        </dt>
                        <dd>
                            Natalie Phillips <span class="pronoun">(she)</span>
                        </dd>
                    </div>
                    <div>
                        <dt>
                            Treasurer
                        </dt>
                        <dd>
                            Beth McCollum <span class="pronoun">(they/she)</span>
                        </dd>
                    </div>
                    <div>
                        <dt>
                            Secretary
                        </dt>
                        <dd>
                            Katy Wolfe <span class="pronoun">(she)</span>
                        </dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <section class="credits">
            <h2>
                Volunteers
            </h2>
            <dl>
                <div>
                    <dt>
                        Lead Engineer
                    </dt>
                    <dd>
                        Graham Watson <span class="pronoun">(he)</span>
                    </dd>
                </div>
                <div>
                    <dt>
                        Engineers
                    </dt>
                    <dd>
                        Dakota Savage <span class="pronoun">(he)</span>
                        <br />
                        Alec Schimmel <span class="pronoun">(he)</span>
                        <br />
                        Madison Turley <span class="pronoun">(she)</span>
                        <br />
                        Sean Wolfe <span class="pronoun">(he)</span>
                    </dd>
                </div>
            </dl>
        </section>
    </div>
</div>

<div class="about">
    <article>
        <h2>
            Transparency
        </h2>
        <p>
            The Vore Arts Fund is committed to radical transparency. Board meetings are open to the public to attend,
            and our corporate records are hosted online for public access.
        </p>
        <ul>
            <li>
                <a href="https://muncieevents.com/tag/2244-vore-arts-fund">Upcoming board meetings</a>
            </li>
            <li>
                <a href="https://drive.google.com/drive/folders/12qrzesRFJqtGFUy-yY7A9eMMvuYpbfPW?usp=sharing">Minutes</a>
            </li>
            <li>
                <a href="https://docs.google.com/document/d/1NqroGwlb--ZSirbxZw0vmS8gvEqQhdjBGiecflSWO8o/edit?usp=drive_link">Bylaws</a>
            </li>
            <li>
                <a href="https://docs.google.com/document/d/1dy-_eFU5J5JgWjS0eXdNWkfw9ukQ6MCCkqOgpQdfAhA/edit?usp=drive_link">Conflict of Interest Policy</a>
            </li>
            <li>
                <a href="https://docs.google.com/document/d/1FBTgQVMzPt9TBxzYdLfTGkleMZaUeMZgeiGYmbHiMuo/edit?usp=drive_link">Document Retention Policy</a>
            </li>
            <li>
                <a href="https://drive.google.com/file/d/13tsXlrP0BRGsiE_vH0WqUBz94QQVTMXN/view?usp=sharing">990-N (2024)</a>
            </li>
        </ul>
    </article>
</div>
