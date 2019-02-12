<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= url('') ?>">
        <?= $this->insert('ixp-logo-header'); ?>
        <?= config('identity.sitename' ) ?>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Member Information
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?= route('customer@details') ?>">
                        Member Details
                    </a>
                    <a class="dropdown-item" href="<?= route( "customer@associates" ) ?>">
                        Associate Members
                    </a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Peering
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                        <a class="dropdown-item" href="<?= url('lg') ?>">
                            Looking Glass
                        </a>
                    <?php endif; ?>
                    <?php if( ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) && !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                        <a class="dropdown-item" href="<?= route('peering-matrix@index') ?>">
                            Peering Matrix
                        </a>
                    <?php endif; ?>
                </div>
            </li>


            <li class="nav-item dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( is_numeric( config( 'grapher.access.ixp' ) ) && config( 'grapher.access.ixp' ) == Entities\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item" href="<?= route( 'statistics/ixp' ) ?>">
                            Overall Peering Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.infrastructure' ) ) && config( 'grapher.access.infrastructure' ) == Entities\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item" href="<?= route( 'statistics/infrastructure' ) ?>">
                            Infrastructure Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.vlan' ) ) && config( 'grapher.access.vlan' ) == Entities\User::AUTH_PUBLIC && config( 'grapher.backends.sflow.enabled' ) ): ?>
                        <a class="dropdown-item" href="<?= route( 'statistics/vlan' ) ?>">
                            VLAN / Per-Protocol Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) == Entities\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item" href="<?= route('statistics/trunk') ?>">
                            Inter-Switch / PoP Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.switch' ) ) && config( 'grapher.access.switch' ) == Entities\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item" href="<?= route('statistics/switch') ?>">
                            Switch Aggregate Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( $this->grapher()->canAccessAllCustomerGraphs() ): ?>
                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="<?= route( 'statistics/members' ) ?>">
                            Member Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                        <div class="dropdown-divider"></div>

                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                            <a class="dropdown-item" href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>"><?= $w['menu'] ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>
        </ul>
        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="<?= route( 'login@showForm' ) ?>">
                    Login
                </a>
            </li>
        </ul>

    </div>
</nav>
