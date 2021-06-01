<nav id="navbar-header" class="navbar navbar-expand-lg navbar-dark bg-dark">

    <button class="navbar-toggler d-block-sm d-md-none" type="button" id="sidebarCollapse" >
        <i id="menu-icon" class="fa fa-bars"></i>
    </button>

    <?= $this->insert('layouts/ixp-logo-header'); ?>

    <button id="navbar-ixp" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fa fa-ellipsis-v"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle center-dd-caret d-flex <?= !request()->is( 'customer/details', 'customer/associates', 'switch/configuration' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Information
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item <?= !request()->is( 'customer/details' ) ?: 'active' ?>" href="<?= route('customer@details') ?>">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Details
                    </a>
                    <a class="dropdown-item <?= !request()->is(  'customer/associates' ) ?: 'active' ?>" href="<?= route( "customer@associates" ) ?>">
                        Associate <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                    </a>
                    <a class="dropdown-item <?= !request()->is( 'switch/configuration' ) ?: 'active' ?>" href="<?= route('switch@configuration') ?>">
                        Switch Configuration
                    </a>
                    <?php if( !config( 'ixp_fe.frontend.disabled.docstore' ) && \IXP\Models\DocstoreDirectory::getHierarchyForUserClass( \IXP\Models\User::AUTH_SUPERUSER ) ): ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item <?= !request()->is( 'docstore*' ) ?: 'active' ?>" href="<?= route('docstore-dir@list' ) ?>">
                            Document Store
                        </a>
                    <?php endif; ?>
                </div>
            </li>


            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle center-dd-caret d-flex <?= !request()->is( 'lg', 'peering-matrix' ) ?: 'active' ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Peering
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'lg' ) ?: 'active' ?>" href="<?= url('lg') ?>">
                            Looking Glass
                        </a>
                    <?php endif; ?>
                    <?php if( !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'peering-matrix' ) ?: 'active' ?>" href="<?= route('peering-matrix@index') ?>">
                            Peering Matrix
                        </a>
                    <?php endif; ?>
                </div>
            </li>

            <?=
                // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                $this->insert('layouts/header-documentation');
            ?>

            <li class="nav-item dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                <a class="nav-link dropdown-toggle center-dd-caret d-flex" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                    <a class="dropdown-item <?= !request()->is( 'statistics/ixp') ?: 'active' ?>" href="<?= route( 'statistics/ixp' ) ?>">
                        Overall Peering Graphs
                    </a>
                    <a class="dropdown-item <?= !request()->is( 'statistics/infrastructure') ?: 'active' ?>" href="<?= route( 'statistics/infrastructure' ) ?>">
                        Infrastructure Graphs
                    </a>

                    <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/vlan') ?: 'active' ?>" href="<?= route( 'statistics/vlan' ) ?>">
                            VLAN / Per-Protocol Graphs
                        </a>
                    <?php endif; ?>

                    <?php if( count( config( 'grapher.backends.mrtg.trunks' ) ?? [] ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/trunk' ) ?: 'active' ?>" href="<?= route('statistics/trunk') ?>">
                            Inter-Switch / PoP Graphs
                        </a>
                    <?php elseif( count( $cbs = d2r( 'CoreBundle' )->getActive() ) ): ?>
                        <a class="dropdown-item <?= !request()->is( 'statistics/core-bundle' ) ?: 'active' ?>" href="<?= route('statistics@core-bundle', $cbs[0]->getId() ) ?>">
                            Inter-Switch / PoP Graphs
                        </a>
                    <?php endif; ?>


                    <a class="dropdown-item <?= !request()->is( 'statistics/switch') ?: 'active' ?>" href="<?= route('statistics/switch') ?>">
                        Switch Aggregate Graphs
                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item <?= !request()->is( 'statistics/members') ?: 'active' ?>" href="<?= route( 'statistics/members' ) ?>">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Graphs
                    </a>
                    <a class="dropdown-item <?= !request()->is( 'statistics/league-table') ?: 'active' ?>" href="<?= route( 'statistics/league-table' ) ?>">
                        League Table
                    </a>


                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                        <div class="dropdown-divider"></div>

                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                            <a class="dropdown-item <?= !request()->is( 'weather-map/'.$k) ?: 'active' ?>" href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>"><?= $w['menu'] ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'public-content/support' ) ?: 'active' ?>" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>


            <?= $this->insert('layouts/staff-links'); ?>

        </ul>

        <form id="div-header-select-customer" class="form-inline my-2 my-lg-0">
            <select id="menu-select-customer" type="select" name="id" class="chzn-select col-xl-7 col-lg-6">
                <option></option>
                <?php foreach( $t->dd_customer_id_name as $k => $i ): ?>
                    <option value="<?= $k ?>"><?= $i ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle center-dd-caret d-flex <?= !request()->is( 'profile', 'api-key/list', 'customer-note/unread-notes' ) ?: 'active' ?>" href="#" id="my-account" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My Account
                </a>
                <ul class="dropdown-menu dropdown-menu-right" id="my-account-dd">

                    <a id="profile" class="dropdown-item <?= !request()->is( 'profile' ) ?: 'active' ?>" href="<?= route( 'profile@edit' ) ?>">Profile</a>

                    <a class="dropdown-item <?= !request()->is( 'api-key/list' ) ?: 'active' ?>" href="<?= route('api-key@list' )?>">API Keys</a>

                    <a id="active-sessions" class="dropdown-item <?= !request()->is( 'active-sessions/list' ) ?: 'active' ?>" href="<?= route('active-sessions@list' )?>">
                        Active Sessions
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item <?= !request()->is( 'customer-note/unread-notes' ) ?: 'active' ?>" href="<?= route( 'customerNotes@unreadNotes' ) ?>">Unread Notes</a>

                    <?php if( count( Auth::getUser()->getActiveCustomers() ) > 1 ): ?>

                        <div class="dropdown-divider"></div>

                        <h6 class="dropdown-header">
                            Switch to:
                        </h6>

                        <?php foreach( Auth::getUser()->getActiveCustomers() as $cust ): ?>

                            <a id="switch-cust-<?= $cust->getId() ?>"
                               class="dropdown-item <?= Auth::getUser()->getCustomer()->getId() != $cust->getId() ?: 'active cursor-default' ?>"
                               <?= Auth::getUser()->getCustomer()->getId() != $cust->getId() ?: "onclick='return false;'" ?>
                               href="<?= Auth::getUser()->getCustomer()->getId() == $cust->getId() ? '#' : route( 'switch-customer@switch' , [ "id" => $cust->getId() ]  ) ?>"
                            >
                                <?= $cust->getName() ?>
                            </a>

                        <?php endforeach; ?>

                    <?php endif; ?>


                    <div class="dropdown-divider"></div>

                    <?php if( session()->exists( "switched_user_from" ) ): ?>
                        <a class="dropdown-item" href="<?= route( 'switch-user@switchBack' ) ?>">Switch Back</a>
                    <?php else: ?>
                        <a id="logout" class="dropdown-item" href="<?= route( 'login@logout' ) ?>">Logout</a>
                    <?php endif; ?>

                </ul>
            </li>

            <li class="nav-item">
                <?php if( session()->exists( "switched_user_from" ) ): ?>
                    <a class="nav-link" href="<?= route( 'switch-user@switchBack' ) ?>">
                        Switch Back
                    </a>
                <?php endif; ?>
            <li>
        </ul>

    </div>
</nav>