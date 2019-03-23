
<nav id="side-navbar" class="col-md-3 col-xl-2 d-none d-md-block sidebar pb-4 pt-4 border-r border-grey bg-grey-lightest text">
    <div class="sidebar-sticky">
        <ul class="nav d-inline">

            <form class="bd-search d-flex align-items-center" method="get" action="<?= route( 'search' ) ?>">

                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for..." name="search">
                    <div class="input-group-append">
                        <button class="btn btn-light input-group-text" type="button" id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">
                            <i class="fa fa-question-circle"></i>
                        </button>
                    </div>
                </div>

            </form>

            <hr class="w-100">

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center my-2">
                <span>IXP CUSTOMER ACTIONS</span>
            </h6>


            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'customer/*' ) ?: 'active' ?>" href="<?= route( 'customer@list' ) ?>">
                    Customers
                </a>
                <?php if( request()->is( 'customer/*' ) || request()->is( 'customer-tag/*' ) ): ?>
                    <ul class="sub-menu">
                        <li class="nav-item" >
                            <a class="nav-link <?= !request()->is( 'customer-tag/*' ) ?: 'active' ?>" href="<?= route('customer-tag@list' ) ?>">
                                Tags
                            </a>
                        </li>
                    </ul>

                <?php endif; ?>
            </li>

            <li class="nav-item">

                <a class="nav-link <?= !request()->is( 'interfaces/virtual*' ) ?: 'active' ?>" href="<?= route( 'interfaces/virtual/list' ) ?>" >
                    Interfaces / Ports
                </a>

                <?php if( request()->is( 'interfaces/*' ) ): ?>
                    <ul class="sub-menu">
                        <li>
                            <a class="nav-link <?= !request()->is( 'interfaces/physical/*' ) ?: 'active' ?>" href="<?= route('interfaces/physical/list' ) ?>">
                                Physical Interface
                            </a>
                        </li>

                        <li>
                            <a class="nav-link <?= !request()->is( 'interfaces/vlan/*' ) ?: 'active' ?>" href="<?= route('interfaces/vlan/list' ) ?>">
                                Vlan Interface
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>

            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'interfaces/sflow-receiver/*' ) ?: 'active' ?>" href="<?= route('interfaces/sflow-receiver/list') ?>">
                    Sflow Receivers
                </a>
            </li>

            <li class="nav-item">

                <a class="nav-link <?= !request()->is( 'patch-panel/*' ) ?: 'active' ?>" href="<?= route('patch-panel/list' ) ?>">
                    Patch Panels
                </a>

                <?php if( request()->is( 'patch-panel/*' ) || request()->is( 'patch-panel-port/*' ) ): ?>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a class="nav-link <?= !request()->is( 'patch-panel-port/*' ) ?: 'active' ?>" href="<?= route('patch-panel-port/list' ) ?>">
                                Patch Panel Port
                            </a>
                        </li>
                    </ul>
                <?php endif;?>

            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'user/*' ) ?: 'active' ?> " href="<?= route('user@list') ?>">
                    Users
                </a>
            </li>

            <li class="nav-item">

                <a class="nav-link <?= !request()->is( 'contact/*' ) ?: 'active' ?> " href="<?= route( 'contact@list' ) ?>">
                    Contacts
                </a>

                <?php if( request()->is( 'contact/*' ) || request()->is( 'contact-group/*' ) ): ?>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a class="nav-link <?= !request()->is( 'contact-group/*' ) ?: 'active' ?> " href="<?= route('contact-group@list' ) ?>">
                                Contact Groups
                            </a>
                        </li>
                    </ul>
                <?php endif;?>
            </li>

            <?php if( !config( 'ixp_fe.frontend.disabled.cust-kit', false ) ): ?>
                <li class="nav-item">
                    <a class="nav-link <?= !request()->is( 'cust-kit/*' ) ?: 'active' ?>" href="<?= route( 'cust-kit@list' ) ?>">
                        Colocated Equipment
                    </a>
                </li>
            <?php endif; ?>



            <h6 class="sidebar-heading d-flex justify-content-between align-items-center mt-4 mb-1 text-muted">
                <span>IXP ADMIN ACTIONS</span>
            </h6>


            <li class="">
                <a class=" <?= !request()->is( 'infrastructure/*' ) ?: 'active' ?>" href="<?= route('infrastructure@list') ?>">
                    Infrastructures
                </a>
            </li>

            <li>
                <a class="tw-block tw-text-white tw-font-medium <?= !request()->is( 'facility/*' ) ?: 'active' ?>" href="<?= route( 'facility@list' ) ?>">
                    Facilities
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= !request()->is( 'rack/*' ) ?: 'active' ?>" href="<?= route('rack@list') ?>">
                    Racks
                </a>
            </li>

            <li class="nav-item" >
                <a id="lhs-menu-switches" class="nav-link <?= !request()->is( 'switch/*' ) ?: 'active' ?>" href="<?= route('switch@list') ?>">
                    Switches
                </a>

                <?php if( request()->is( 'switch/*' ) || request()->is( 'switch-port/*' ) ): ?>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a id="lhs-menu-switch-ports" class="nav-link <?= request()->is( 'switch-port/*' ) && !request()->is( 'switch-port/unused-optics' ) && !request()->is( 'switch-port/optic-inventory' ) && !request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>" href="<?= route( "switch-port@list" ) ?>">
                                Switch Ports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= route( "switch-port@unused-optics" ) ?>" class="nav-link <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/unused-optics' ) ? 'active' : '' ?>">
                                Unused Optics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= route( "switch-port@optic-inventory" ) ?>" class="nav-link <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/optic-inventory' )  || request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>">
                                Optic Inventory
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>

            <li class="nav-item" >
                <a href="<?= route('router@list' ) ?>" class="nav-link <?= request()->is( 'router/*' ) && !request()->is( 'router/status' ) ? 'active' : '' ?>" >
                    Routers
                </a>

                <?php if( request()->is( 'router/*' ) ): ?>
                <ul class="sub-menu">
                    <li class="nav-item sub-menu" >
                        <a href="<?= route('router@status' ) ?>" class="nav-link <?= request()->is( 'router/status' ) ? 'active' : '' ?> " >
                            Live Status
                        </a>
                    </li>
                </ul>
                <?php endif;?>

            </li>

            <li class="nav-item" >
                <a href="<?= route('console-server@list' ) ?>" class="nav-link <?= !request()->is( 'console-server/*' ) ?: 'active' ?>">
                    Console Servers
                </a>

                <?php if( request()->is( 'console-server/*' ) || request()->is( 'console-server-connection/*' ) ): ?>
                    <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection', false ) ): ?>
                        <ul class="sub-menu">
                            <li class="nav-item" >
                                <a href="<?= route('console-server-connection@list' ) ?>" class="nav-link <?= !request()->is( 'console-server-connection/*' ) ?: 'active' ?>">
                                    Console Server Connections
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>

            </li>



            <?php if( config( 'ixp_fe.frontend.beta.core_bundles', false ) ): ?>

                <li class="nav-item" >
                    <a href="<?= route('core-bundle/list' ) ?>" class="nav-link <?= !request()->is( 'interfaces/core-bundle/*' ) ?: 'active' ?>">
                        Core Bundles
                    </a>
                </li>

            <?php endif; ?>

            <li class="nav-item" >
                <a href="<?= route('ip-address@list', [ 'protocol' => 6 ] ) ?>" class="nav-link">
                    IP Addresses
                </a>
                <?php if( request()->is( 'ip-address/*' ) ): ?>
                    <ul class="sub-menu">
                        <li class="nav-item">
                            <a href="<?= route('ip-address@list', [ 'protocol' => 4 ] ) ?>" class="nav-link <?= request()->route()->parameter('protocol') == '4' ? 'active' : '' ?>">
                                &nbsp;&nbsp;&nbsp;&nbsp;IPv4 Addresses
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?= route('ip-address@list', [ 'protocol' => 6 ] ) ?>" class="nav-link <?= request()->route()->parameter('protocol') == '6' ? 'active' : '' ?>">
                                &nbsp;&nbsp;&nbsp;&nbsp;IPv6 Addresses
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>

            <li class="nav-item">
                <a href="<?= route( 'layer2-address@list' ) ?>" class="nav-link">
                    MAC Addresses
                </a>
            </li>
            <?php if( request()->is( 'mac-address/*' ) || request()->is( 'layer2-address/*' ) ): ?>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="<?= route( 'layer2-address@list' ) ?>" class="nav-link <?= !request()->is( 'layer2-address/*' ) ?: 'active' ?> ">
                            Configured Addresses
                        </a>
                    </li>

                    <?php if( !config( 'ixp_fe.frontend.disabled.mac-address', false ) ): ?>

                        <li class="nav-item" >
                            <a href="<?= route('mac-address@list') ?>" class="nav-link <?= !request()->is( 'mac-address/*' ) ?: 'active' ?>">
                                Discovered Addresses
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>
            <?php endif; ?>


            <li class="nav-item" >
                <a href="<?= route('vendor@list' ) ?>" class="nav-link <?= !request()->is( 'vendor/*' ) ?: 'active' ?>">
                    Vendors
                </a>
            </li>


            <li class="nav-item" >
                <a href="<?= route('vlan@list' ) ?>" class="nav-link <?= request()->is( 'vlan/*' ) && !request()->is( 'vlan/private' ) ? 'active' : '' ?>">
                    VLANs
                </a>
            </li>

            <?php if( request()->is( 'vlan/*' ) || request()->is( 'network-info/*' ) ): ?>
                <ul class="sub-menu">
                    <li class="nav-item" >
                        <a href="<?= route('network-info@list' ) ?>" class="nav-link <?= !request()->is( 'network-info/*' ) ?: 'active' ?>">
                            Network Information
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= route( 'vlan@private' ) ?>" class="nav-link <?= !request()->is( 'vlan/private' ) ?: 'active' ?>">
                            Private VLANs
                        </a>
                    </li>
                </ul>

            <?php endif; ?>

            <li class="nav-item" >
                <a href="<?= route( 'irrdb-config@list' ) ?>" class="nav-link <?= !request()->is( 'irrdb-config/*' ) ?: 'active' ?>">
                    IRRDB Configuration
                </a>
            </li>

            <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                <li class="nav-item" >
                    <a href="<?= route( 'rs-prefixes@list' ) ?>" class="nav-link <?= !request()->is( 'rs-prefixes/*' ) ?: 'active' ?>">
                        Route Server Prefixes
                    </a>
                </li>
            <?php endif; ?>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center mt-4 mb-1 text-muted">
                <span>IXP STATISTICS</span>
            </h6>

            <li class="nav-item" >
                <a href="<?= route( 'statistics/members' ) ?>" class="nav-link <?= !request()->is( 'statistics/members' ) ?: 'active' ?>">
                    Member Statistics
                </a>
            </li>

            <?php if( !config( 'ixp_fe.frontend.disabled.logo', true ) ): ?>
                <li class="nav-item">
                    <a href="<?= route('logo@logos' ) ?>" class="nav-link <?= !request()->is( 'customer-logo/logos' ) ?: 'active' ?>">
                        Member Logos
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a href="<?= route( 'statistics/league-table' ) ?>" class="nav-link <?= !request()->is( 'statistics/league-table' ) ?: 'active' ?>">
                    League Table
                </a>
            </li>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center mt-4 mb-1 text-muted">
                IXP UTILITIES
            </h6>

            <?php if( Gate::allows( 'viewTelescope' ) ): ?>
                <li class="nav-item" >
                    <a href="<?= route( 'telescope' ) ?>" class="nav-link" target="_ixpm_telescope">
                        Laravel Telescope
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item" >
                <a href="<?= route( 'utils/phpinfo' ) ?>" class="nav-link <?= !request()->is( 'utils/phpinfo' ) ?: 'active' ?>">
                    PHP Info
                </a>
            </li>

            <li class="nav-item" >
                <a href="<?= route( 'login-history@list' ) ?>" class="nav-link <?= !request()->is( 'login-history/*' ) ?: 'active' ?>">
                    Last Logins
                </a>
            </li>

        </ul>
    </div>
</nav>

<div class="modal fade" id="searchHelpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Search Help</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            The search box allows for an efficient database search via a number of parameters:
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt>IPv4 Address</dt>
                            <dd>
                                Full address as <code>a.b.c.d</code> or last octet as <code>.d</code>
                            </dd>
                            <dt>AS Number / Macro</dt>
                            <dd>
                                Enter ASN as <code>XXX</code> or <code>asXXX</code> and AS macro as <code>as-XXX</code>
                            </dd>
                            <dt>Usernames</dt>
                            <dd>
                                Find usernames <em>starting with</em> <code>xxx</code> by entering <code>@xxx</code>
                            </dd>
                            <dt>Route Server Prefix</dt>
                            <dd>
                                Enter IPv4 <code>a.b.c.d/x</code> or IPv6 <code>a:b::/x</code>
                            </dd>
                            <dt>Patch Panel Port</dt>
                            <dd>
                                Find a patch panel port by its ID: <code>PPP-xxxx</code>.<br>
                                Wildcard search on colo references: <code>xc:xxx</code>.
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt>IPv6 Address</dt>
                            <dd>
                                Full (compact) address as <code>a:b::h</code> or last section as <code>:h</code>
                            </dd>
                            <dt>MAC Address</dt>
                            <dd>
                                Enter as <code>xxxxxxxxxxxx</code> or <code>xx:xx:xx:xx:xx:xx</code>
                            </dd>
                            <dt>Email Addresses</dt>
                            <dd>
                                Find contacts / users via full email address <code>xxx@example.com</code>
                            </dd>
                            <dt>Wildcard</dt>
                            <dd>
                                Any other text is searched as <code>%xxx%</code> on customer details
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
