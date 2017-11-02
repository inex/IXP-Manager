<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= url('') ?>">
                <?= config('identity.sitename' ) ?>
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                            <li><a href="<?= url('lg') ?>">Looking Glass</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?php url( 'public-statistics/public' ) ?>">Overall Peering Graphs</a>
                        </li>
                        <li>
                            <a href="<?php url( 'public-statistics/trunks' ) ?>">Inter-Switch / PoP Graphs</a>
                        </li>
                        <li>
                            <a href="<?php url( 'public-statistics/switches' ) ?>">Switch Aggregate Graphs</a>
                        </li>
                        <li class="divider">
                        </li>
                        <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                            <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                                <li>
                                    <a href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>"><?= $w['menu'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="">
                    <a href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">Support</a>
                </li>
                <li>
                    <a href="http://www.ixpmanager.org/" target="_blank">About</a>
                </li>
                <li class="">
                    <a href="<?= url( '/auth/login' ) ?>">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
