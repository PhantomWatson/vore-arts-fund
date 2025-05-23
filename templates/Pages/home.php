<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var bool $fundingCycleIsCurrent
 * @var bool $isStaging
 * @var string[] $cycleCategories
 * @var \App\Model\Entity\Article|null $article
 */
?>
<div id="home">
    <div class="row">
        <div class="col-sm-8">
            <section>
                <h1>
                    The Vore Arts Fund
                </h1>
                <div class="info">
                    <p>
                        The Vore Arts Fund is a 501(c)(3) nonprofit dedicated to empowering Muncie's arts community by eliminating financial barriers to creating commercial art, music, theater, and art education. Through zero‐interest, unconditionally forgivable loans, we enable local artists, performers, producers, and educators to launch and sustain creative ventures that enrich Muncie's cultural landscape.
                    </p>
                </div>
            </section>

            <?php if ($article): ?>
                <section class="latest-news card">
                    <div class="card-body">
                        <?= $this->element('Articles/article', ['article' => $article, 'isLatest' => true]) ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
        <div class="col-sm-4">
            <section class="card">
                <div class="card-body">
                    <h1 class="card-title">
                        Mailing list
                    </h1>
                    <p class="card-text">
                        Sign up to our mailing list to stay up-to-date on new developments from the Vore Arts Fund,
                        opportunities to support our mission, and announcements about applying for funding and voting on
                        applications.
                    </p>
                    <p class="card-text text-center">
                        <?= $this->Html->link(
                            'Sign up',
                            ['controller' => 'MailingList', 'action' => 'signup'],
                            ['class' => 'btn btn-primary'],
                        ) ?>
                    </p>
                </div>
            </section>
            <section class="card">
                <div class="card-body">
                    <h1 class="card-title">
                        Follow us
                    </h1>
                    <ul class="list-unstyled social-media-links">
                        <li>
                            <a href="https://www.facebook.com/VoreArtsFund">
                                <i class="fa-brands fa-facebook" title="Facebook" aria-hidden="true"></i>
                                <span class="sr-only">Facebook:</span>
                                <span class="handle">facebook.com/VoreArtsFund</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://voreartsfund.bsky.social/">
                                <i class="fa-brands fa-bluesky" title="Bluesky" aria-hidden="true"></i>
                                <span class="sr-only">Bluesky:</span>
                                <span class="handle">voreartsfund.bsky.social</span>
                            </a>
                        </li>
                        <li>
                            <a href="https://muncieevents.com/tag/2244-vore-arts-fund">
                                <i class="icon-me-logo" title="Muncie Events"></i>
                                <span class="handle">Muncie Events</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <?= $this->element('home/funding_cycles_overview') ?>
</div>
