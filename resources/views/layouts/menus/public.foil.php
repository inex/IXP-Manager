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

                <li>
                    <a href="<?= url('') ?>">Home</a>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Member Information <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= route( "customer@details" ) ?>">Member Details</a>
                        </li>
                        <li>
                            <a href="<?= route( "customer@associates" ) ?>">Associate Members</a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                            <li><a href="<?= url('lg') ?>">Looking Glass</a></li>
                        <?php endif; ?>
                        <?php if( ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) && !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                            <li><a href="<?= route('peering-matrix@index') ?>">Peering Matrix</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php if( is_numeric( config( 'grapher.access.ixp' ) ) && config( 'grapher.access.ixp' ) == Entities\User::AUTH_PUBLIC ): ?>
                            <li>
                                <a href="<?= route( 'statistics/ixp' ) ?>">Overall Peering Graphs</a>
                            </li>
                        <?php endif; ?>
                        <?php if( is_numeric( config( 'grapher.access.infrastructure' ) ) && config( 'grapher.access.infrastructure' ) == Entities\User::AUTH_PUBLIC ): ?>
                            <li>
                                <a href="<?= route( 'statistics/infrastructure' ) ?>">Infrastructure Graphs</a>
                            </li>
                        <?php endif; ?>
                        <?php if( is_numeric( config( 'grapher.access.vlan' ) ) && config( 'grapher.access.vlan' ) == Entities\User::AUTH_PUBLIC && config( 'grapher.backends.sflow.enabled' ) ): ?>
                            <li>
                                <a href="<?= route( 'statistics/vlan' ) ?>">VLAN / Per-Protocol Graphs</a>
                            </li>
                        <?php endif; ?>
                        <?php if( is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) == Entities\User::AUTH_PUBLIC ): ?>
                            <li>
                                <a href="<?= route('statistics/trunk') ?>">Inter-Switch / PoP Graphs</a>
                            </li>
                        <?php endif; ?>
                        <?php if( is_numeric( config( 'grapher.access.switch' ) ) && config( 'grapher.access.switch' ) == Entities\User::AUTH_PUBLIC ): ?>
                            <li>
                                <a href="<?= route('statistics/switch') ?>">Switch Aggregate Graphs</a>
                            </li>
                        <?php endif; ?>


                        <?php if( $this->grapher()->canAccessAllCustomerGraphs() ): ?>

                            <li class="divider"></li>

                            <li>
                                <a href="<?= route( 'statistics/members' ) ?>">Member Graphs</a>
                            </li>

                        <?php endif; ?>


                        <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>

                            <li class="divider"></li>

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
