<footer class="footer p-3 mt-auto bg-dark">
    <div class="navbar-nav w-100 text-light text-center">
        <div>
            <small>
                IXP Manager V<?= APPLICATION_VERSION ?>

                &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

                <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
                    Generated in
                    <?= sprintf( "%0.3f", microtime(true) - APPLICATION_STARTTIME ) ?>
                    seconds
                <?php else: ?>
                    Copyright &copy; 2009 - <?= now()->format('Y') ?> Internet Neutral Exchange Association CLG
                <?php endif; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                Discover IXP Manager at:

                <a href="https://www.ixpmanager.org/">
                    <i class="fa fa-globe fa-inverse mx-1"></i>
                </a>

                <a href="https://www.youtube.com/channel/UCeW2fmMTBtE4fnlmg-2-evA">
                    <i class="fa fa-youtube-play fa-inverse mx-1" ></i>
                </a>

                <a  href="https://www.linkedin.com/company/inex---internet-neutral-exchange/">
                    <i class="fa fa-linkedin fa-inverse mx-1"></i>
                </a>

                <a  href="https://github.com/inex/IXP-Manager">
                    <i class="fa fa-github fa-inverse mx-1"></i>
                </a>

                <a  href="https://docs.ixpmanager.org/latest/">
                    <i class="fa fa-book fa-inverse mx-1"></i>
                </a>

            </small>
        </div>
    </div>
</footer>