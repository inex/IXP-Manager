<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <?= $this->insert('layouts/ixp-logo-header'); ?>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-ellipsis-v"></i>
    </button>

        <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">

            <li class="nav-item <?= !request()->is( 'dashboard' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= url('') ?>">
                   Home
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?= !request()->is( 'customer/*' , 'switch/configuration', 'docstore/*' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Information
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item <?= !request()->is( 'customer/details' ) ?: 'active' ?>" href="<?= route('customer@details') ?>">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Details
                    </a>
                    <a class="dropdown-item <?= !request()->is( 'customer/associates' ) ?: 'active' ?>" href="<?= route( "customer@associates" ) ?>">
                        Associate <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                    </a>
                    <a class="dropdown-item <?= !request()->is( 'switch/configuration' ) ?: 'active' ?>" href="<?= route('switch@configuration') ?>">
                        Switch Configuration
                    </a>

                    <?php if( !config( 'ixp_fe.frontend.disabled.docstore' ) && \IXP\Models\DocstoreDirectory::getHierarchyForUserClass( \IXP\Models\User::AUTH_CUSTUSER ) ): ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item <?= request()->is( 'docstore*' ) && !request()->is( 'docstorec*' ) ? 'active' : '' ?>" href="<?= route('docstore-dir@list' ) ?>">
                            Document Store
                        </a>
                    <?php endif; ?>

                    <?php if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) && \IXP\Models\DocstoreCustomerFile::getListingForAllDirectories( Auth::user()->getCustomer()->getId(),\IXP\Models\User::AUTH_CUSTUSER )  ): ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item <?= !request()->is( 'docstore-c*' ) ?: 'active' ?>" href="<?= route('docstore-c-dir@list', [ 'cust' => Auth::user()->getCustomer()->getId() ] ) ?>">
                            My Documents
                        </a>
                    <?php endif; ?>

                </div>

            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?= !request()->is( 'peering-manager' , 'lg', 'peering-matrix', 'rs-prefixes/list' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Peering
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( !config( 'ixp_fe.frontend.disabled.peering-manager', false ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'peering-manager' ) ?: 'active' ?>" href="<?= route('peering-manager@index') ?>">
                            Peering Manager
                        </a>
                    <?php endif; ?>
                    <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                        <?php if( Auth::user()->getCustomer()->isRouteServerClient() ): ?>
                            <a class="dropdown-item <?= !request()->is( 'rs-prefixes/list' ) ?: 'active' ?>" href="<?= route('rs-prefixes@list') ?>">
                                Route Server Prefixes
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'lg'  ) ?: 'active' ?>" href="<?= url('lg') ?>">
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

            <?php
                // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                // Add a skinned file for your IXP to override the sample
                echo $this->insert('layouts/header-documentation');
            ?>

            <li class="nav-item dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item <?= !request()->is( 'statistics/member' ) ?: 'active' ?>" href="<?= route( 'statistics@member' ) ?>">
                        My Statistics
                    </a>

                    <?php if( config('grapher.backends.sflow.enabled') ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/p2p/*' ) ?: 'active' ?>" href="<?= route( 'statistics@p2p', ['cid' => Auth::user()->getCustomer()->getId() ] ) ?>">
                            My Peer to Peer Traffic
                        </a>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>

                    <?php if( is_numeric( config( 'grapher.access.ixp' ) ) && config( 'grapher.access.ixp' ) <= Auth::user()->getPrivs() ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/ixp' ) ?: 'active' ?>" href="<?= route( 'statistics/ixp' ) ?>">
                            Overall Peering Graphs
                        </a>
                    <?php endif; ?>
                    <?php if( is_numeric( config( 'grapher.access.infrastructure' ) ) && config( 'grapher.access.infrastructure' )  <= Auth::user()->getPrivs() ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/infrastructure' ) ?: 'active' ?>" href="<?= route( 'statistics/infrastructure' ) ?>">
                            Infrastructure Graphs
                        </a>
                    <?php endif; ?>
                    <?php if( is_numeric( config( 'grapher.access.vlan' ) ) && config( 'grapher.access.vlan' ) <= Auth::user()->getPrivs() && config( 'grapher.backends.sflow.enabled' ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/vlan' ) ?: 'active' ?>" href="<?= route( 'statistics/vlan' ) ?>">
                            VLAN / Per-Protocol Graphs
                        </a>
                    <?php endif; ?>


                    <?php if( is_numeric( config( 'grapher.access.trunk' ) ) && config( 'grapher.access.trunk' ) <= Auth::user()->getPrivs() ): ?>
                        <?php if( count( config( 'grapher.backends.mrtg.trunks' ) ?? [] ) ): ?>
                            <a class="dropdown-item <?= !request()->is( 'statistics/trunk' ) ?: 'active' ?>" href="<?= route('statistics/trunk') ?>">
                                Inter-Switch / PoP Graphs
                            </a>
                        <?php elseif( count( $cbs = d2r( 'CoreBundle' )->getActive() ) ): ?>
                            <a class="dropdown-item <?= !request()->is( 'statistics/core-bundle' ) ?: 'active' ?>" href="<?= route('statistics@core-bundle', $cbs[0]->getId() ) ?>">
                                Inter-Switch / PoP Graphs
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>


                    <?php if( is_numeric( config( 'grapher.access.switch' ) ) && config( 'grapher.access.switch' ) <= Auth::user()->getPrivs() ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/switch' ) ?: 'active' ?>" href="<?= route('statistics/switch') ?>">
                            Switch Aggregate Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( $this->grapher()->canAccessAllCustomerGraphs() ): ?>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item <?= !request()->is( 'statistics/members' ) ?: 'active' ?>" href="<?= route( 'statistics/members' ) ?>">
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Graphs
                        </a>

                    <?php endif; ?>



                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>

                        <div class="dropdown-divider"></div>

                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                            <a class="dropdown-item <?= !request()->is( 'weather-map/'. $k ) ?: 'active' ?>" href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>">
                                <?= $w['menu'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is(  'public-content/support' ) ?: 'active' ?>" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>
        </ul>

        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle <?= !request()->is( 'profile' , 'api-key/list' ) ?: 'active' ?>" href="#" id="my-account" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My Account
                </a>
                <ul class="dropdown-menu dropdown-menu-right" id="my-account-dd">

                    <a id="profile" class="dropdown-item <?= !request()->is( 'profile' ) ?: 'active' ?>" href="<?= route( 'profile@edit' ) ?>">
                        Profile
                    </a>

                    <a class="dropdown-item <?= !request()->is( 'api-key/list' ) ?: 'active' ?>" href="<?= route('api-key@list' )?>">
                        API Keys
                    </a>

                    <a id="active-sessions" class="dropdown-item <?= !request()->is( 'active-sessions/list' ) ?: 'active' ?>" href="<?= route('active-sessions@list' )?>">
                        Active Sessions
                    </a>

                    <?php if( count( Auth::getUser()->getActiveCustomers() ) > 1 ): ?>

                        <div class="dropdown-divider"></div>

                        <h6 class="dropdown-header">
                            Switch to:
                        </h6>

                        <?php foreach( Auth::getUser()->getActiveCustomers() as $cust ): ?>

                            <a id="switch-cust-<?= $cust->getId() ?>" class="dropdown-item <?= Auth::getUser()->getCustomer()->getId() != $cust->getId() ?: 'active cursor-default' ?>" <?= Auth::getUser()->getCustomer()->getId() != $cust->getId() ?: "onclick='return false;'" ?> href="<?= Auth::getUser()->getCustomer()->getId() == $cust->getId() ? '#' : route( 'switch-customer@switch' , [ "id" => $cust->getId() ]  ) ?>">
                                <?= $cust->getName() ?>
                            </a>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    <div class="dropdown-divider"></div>

                    <a id="logout" class="dropdown-item" href="<?= route( 'login@logout' ) ?>">
                        Logout
                    </a>

                </ul>
            </li>

            <li class="nav-item">
                <?php if( session()->exists( "switched_user_from" ) ): ?>
                    <a class="nav-link" href="<?= route( 'switch-user@switchBack' ) ?>">
                        Switch Back
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="<?= route( 'login@logout' ) ?>">
                        Logout
                    </a>
                <?php endif; ?>
            <li>
        </ul>
    </div>
</nav>