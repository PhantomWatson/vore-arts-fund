<?php
/**
 * @var \App\Model\Entity\FundingCycle $fundingCycle
 * @var \App\View\AppView $this
 * @var bool $fundingCycleIsCurrent
 * @var array|null $votingInfo
 * @var bool $hasVoted
 * @var bool $isStaging
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
                        The Vore Arts Fund is a 501(c)(3) not-for-profit program for supporting the Muncie, Indiana,
                        arts community by distributing zero-interest, unconditionally forgivable loans to cover the
                        up-front costs of producing for-profit art, music, theater, and art education.
                    </p>
                    <p>
                        We're currently fundraising for our first funding cycle, and applications for funding are
                        not yet open to the public. Stay tuned for updates.
                    </p>
                    <p>
                        Please email <a href="mailto:info@voreartsfund.org">info@voreartsfund.org</a> if you have any
                        questions.
                    </p>
                </div>
            </section>
            <section>
                <h1>
                    Coverage on Indiana Public Radio
                </h1>
                <p>
                    We'd like to thank Thomas Ouellette and Indiana Public Radio for
                    <a href="https://indianapublicradio.org/news/2024/05/a-new-fund-aims-at-helping-struggling-artists-bring-their-projects-to-life/">their recent story about the Vore Arts Fund</a>.
                    Also worth reading is <a href="https://indianapublicradio.org/news/2023/10/new-national-study-highlights-economic-impact-of-arts-culture-on-indiana-cities/">their coverage of National Arts and Economic Prosperity 6</a>,
                    a 2023 study that measured the economic impact of the support given by arts and culture nonprofits.
                </p>
            </section>
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
                        <li>
                            <a href="https://github.com/PhantomWatson/vore-arts-fund">
                                <i class="fa-brands fa-github" title="GitHub" aria-hidden="true"></i>
                                <span class="sr-only">GitHub:</span>
                                <span class="handle">PhantomWatson/vore-arts-fund</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <?= $this->element('home/voting', [
        'votingCycle' => $votingInfo['cycle'] ?? null,
        'votingProjectCount' => $votingInfo['projectCount'] ?? null,
        'hasVoted' => $hasVoted,
    ]) ?>

    <?= $this->element('home/applying') ?>
</div>
