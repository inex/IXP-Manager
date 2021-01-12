<footer class="footer p-3 mt-auto bg-dark">
    <div class="navbar-nav w-100 text-light text-center">
        <div>
            <small>

                IXP Manager V<?= APPLICATION_VERSION ?>

                |

                <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
                    Generated in
                    <?= sprintf( "%0.3f", microtime(true) - APPLICATION_STARTTIME ) ?>
                    seconds
                <?php else: ?>
                    Copyright &copy; 2009 - <?= now()->format('Y') ?> Internet Neutral Exchange Association CLG
                <?php endif; ?>
                |
                Discover INEX at:
                <a href="https://www.inex.ie/">
                    <i class="fa fa-globe fa-inverse mx-1"></i>
                </a>

                <a href="https://www.linkedin.com/groups/1853398/">
                    <i class="fa fa-linkedin fa-inverse mx-1"></i>
                </a>

                <a href="https://www.facebook.com/comepeerwithme/">
                    <i class="fa fa-facebook fa-inverse mx-1" ></i>
                </a>

                <a  href="https://twitter.com/ComePeerWithMe">
                    <i class="fa fa-twitter fa-inverse mx-1"></i>
                </a>

                <a  href="https://github.com/inex">
                    <i class="fa fa-github fa-inverse mx-1"></i>
                </a>

            </small>
        </div>
    </div>
</footer>