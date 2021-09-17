<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <?= $this->insert('layouts/ixp-logo-header'); ?>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-ellipsis-v"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <?php if( config( 'ixp_fe.customer.details_public') ): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= !request()->is( 'customer/details', 'customer/associates' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Information
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item <?= !request()->is( 'customer/details' ) ?: 'active' ?>" href="<?= route('customer@details') ?>">
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Details
                        </a>
                        <a class="dropdown-item <?= !request()->is( 'customer/associates' ) ?: 'active' ?>" href="<?= route( "customer@associates" ) ?>">
                            Associate <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                        </a>

                        <?php if( !config( 'ixp_fe.frontend.disabled.docstore' ) && \IXP\Models\DocstoreDirectory::getHierarchyForUserClass( \IXP\Models\User::AUTH_PUBLIC ) ): ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item <?= !request()->is( 'docstore*' ) ?: 'active' ?>" href="<?= route('docstore-dir@list' ) ?>">
                                Document Store
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>

            <?php if( !config('ixp_fe.frontend.disabled.lg' ) && ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) && !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= !request()->is( 'lg', 'peering-matrix' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Peering
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                            <a class="dropdown-item <?= !request()->is( 'lg' ) ?: 'active' ?>" href="<?= url('lg') ?>">
                                Looking Glass
                            </a>
                        <?php endif; ?>
                        <?php if( ixp_min_auth( config( 'ixp.peering-matrix.min-auth' ) ) && !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                            <a class="dropdown-item <?= !request()->is( 'peering-matrix' ) ?: 'active' ?>" href="<?= route('peering-matrix@index') ?>">
                                Peering Matrix
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endif; ?>

            <li class="nav-item dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( is_numeric( config( 'grapher.access.ixp' ) ) && config( 'grapher.access.ixp' ) === \IXP\Models\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/ixp*' ) ?: 'active' ?>" href="<?= route( 'statistics@ixp' ) ?>">
                            Overall Peering Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.infrastructure' ) ) && config( 'grapher.access.infrastructure' ) === \IXP\Models\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/infrastructure*' ) ?: 'active' ?>" href="<?= route( 'statistics@infrastructure' ) ?>">
                            Infrastructure Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.vlan' ) ) && config( 'grapher.access.vlan' ) === \IXP\Models\User::AUTH_PUBLIC && config( 'grapher.backends.sflow.enabled' ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/vlan*' ) ?: 'active' ?>" href="<?= route( 'statistics@vlan' ) ?>">
                            VLAN / Per-Protocol Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.location' ) ) && config( 'grapher.access.location' ) === \IXP\Models\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/location' ) ?: 'active' ?>" href="<?= route('statistics@location' ) ?>">
                            Facility Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) === \IXP\Models\User::AUTH_PUBLIC ): ?>
                        <?php if( count( config( 'grapher.backends.mrtg.trunks' ) ?? [] ) ): ?>
                            <a class="dropdown-item <?= !request()->is( 'statistics/trunk*' ) ?: 'active' ?>" href="<?= route('statistics@trunk') ?>">
                                Inter-Switch / PoP Graphs
                            </a>
                        <?php elseif( $cb = \IXP\Models\CoreBundle::active()->first() ): ?>
                            <a class="dropdown-item <?= !request()->is( 'statistics/core-bundle' ) ?: 'active' ?>" href="<?= route('statistics@core-bundle', $cb->id ) ?>">
                                Inter-Switch / PoP Graphs
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if( is_numeric( config( 'grapher.access.switch' ) ) && config( 'grapher.access.switch' ) === \IXP\Models\User::AUTH_PUBLIC ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/switch' ) ?: 'active' ?>" href="<?= route('statistics@switch') ?>">
                            Switch Aggregate Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( $this->grapher()->canAccessAllCustomerGraphs() ): ?>
                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item <?= !request()->is( 'statistics/members' ) ?: 'active' ?>" href="<?= route( 'statistics@members' ) ?>">
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                        <div class="dropdown-divider"></div>

                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                            <a class="dropdown-item <?= !request()->is( 'weather-map/' . $k ) ?: 'active' ?>" href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>"><?= $w['menu'] ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'public-content/support' ) ?: 'active' ?>" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>
        </ul>
        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item">
                <?php if( Auth::check() ): ?>
                    <a class="nav-link" href="<?= route( 'login@logout' ) ?>">
                        Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link <?= !request()->is( 'login' ) ?: 'active' ?>" href="<?= route( 'login@showForm' ) ?>">
                        Login
                    </a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>