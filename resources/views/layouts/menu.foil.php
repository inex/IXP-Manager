<nav id="side-navbar" class="col-md-3 col-xl-2 d-none d-md-block sidebar border-r border-grey bg-grey-lightest text">
    <div class="sidebar-sticky">
        <ul class="nav d-inline ">
            <form class="bd-search d-flex align-items-center tw-border-grey-lighter tw-border-r-4 tw-py-4" method="get" action="<?= route( 'search' ) ?>">
                <div class="input-group tw-pr-4">
                    <input type="text" class="form-control" placeholder="Search for..." name="search">
                    <div class="input-group-append">
                        <button class="btn btn-light input-group-text" type="button" id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">
                            <i class="fa fa-question-circle"></i>
                        </button>
                    </div>
                </div>
            </form>

            <hr class="w-100 tw-my-0" style="margin-left: -10px ">

            <h6>
                <span>IXP <?= strtoupper( config( 'ixp_fe.lang.customer.one' ) ) ?> ACTIONS</span>
            </h6>

            <li class="<?= request()->is( 'customer/*' ) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= route( 'customer@list' ) ?>">
                    <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
                </a>
            </li>

            <?php if( request()->is( 'customer/*' ) || request()->is( 'customer-tag/*' ) || request()->is( 'customer-logo*' ) ): ?>
                <ul>
                    <?php if( !config( 'ixp_fe.frontend.disabled.logo', true ) ): ?>
                        <li class="nav-sub-menu-item <?= !request()->is( 'customer-logo/logos' ) ?: 'active' ?>">
                            <a href="<?= route('logo@logos' ) ?>" class="nav-link">
                                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Logos
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-sub-menu-item <?= !request()->is( 'customer-tag/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('customer-tag@list' ) ?>">
                            Tags
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <li class="<?= !request()->is( 'interfaces/virtual*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route( 'virtual-interface@list' ) ?>" >
                    Interfaces / Ports
                </a>
            </li>

            <?php if( request()->is( 'interfaces/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= !request()->is( 'interfaces/physical/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('physical-interface@list' ) ?>">
                            Physical Interfaces
                        </a>
                    </li>

                    <li class="nav-sub-menu-item <?= !request()->is( 'interfaces/vlan/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('vlan-interface@list' ) ?>">
                            Vlan Interfaces
                        </a>
                    </li>

                    <li class="nav-sub-menu-item <?= !request()->is( 'interfaces/sflow-receiver/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('sflow-receiver@list') ?>">
                            Sflow Receivers
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <li class="<?= !request()->is( 'patch-panel/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route('patch-panel@list' ) ?>">
                    Patch Panels
                </a>
            </li>

            <?php if( request()->is( 'patch-panel/*' ) || request()->is( 'patch-panel-port/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= !request()->is( 'patch-panel-port/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('patch-panel-port@list' ) ?>">
                            Patch Panel Port
                        </a>
                    </li>
                </ul>
            <?php endif;?>

            <li class="<?= !request()->is( 'user/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route('user@list') ?>">
                    Users
                </a>
            </li>

            <li class="<?= !request()->is( 'contact/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route( 'contact@list' ) ?>">
                    Contacts
                </a>
            </li>

            <?php if( request()->is( 'contact/*' ) || request()->is( 'contact-group/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= !request()->is( 'contact-group/*' ) ?: 'active' ?>">
                        <a class="nav-link" href="<?= route('contact-group@list' ) ?>">
                            Contact Groups
                        </a>
                    </li>
                </ul>
            <?php endif;?>

            <?php if( !config( 'ixp_fe.frontend.disabled.cust-kit', false ) ): ?>
                <li class="<?= !request()->is( 'cust-kit/*' ) ?: 'active' ?>">
                    <a class="nav-link" href="<?= route( 'cust-kit@list' ) ?>">
                        Colocated Equipment
                    </a>
                </li>
            <?php endif; ?>

            <h6>
                <span>IXP ADMIN ACTIONS</span>
            </h6>

            <li class="<?= !request()->is( 'console-server/*' ) ?: 'active' ?>">
                <a href="<?= route('console-server@list' ) ?>" class="nav-link">
                    Console Servers
                </a>
            </li>

            <?php if( request()->is( 'console-server/*' ) || request()->is( 'console-server-connection/*' ) ): ?>
                <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection', false ) ): ?>
                    <ul>
                        <li class="nav-sub-menu-item <?= !request()->is( 'console-server-connection/*' ) ?: 'active' ?>" >
                            <a href="<?= route('console-server-connection@list' ) ?>" class="nav-link">
                                Console Server Connections
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <li class="<?= !request()->is( 'interfaces/core-bundle/*' ) ?: 'active' ?>" >
                <a href="<?= route('core-bundle@list' ) ?>" class="nav-link">
                    Core Bundles
                </a>
            </li>


            <?php /**************************************** DOCSTORE ****************************************/ ?>

            <?php if( !config( 'ixp_fe.frontend.disabled.docstore' ) ): ?>
                <li class="<?= !request()->is( 'docstorec*' ) && request()->is( 'docstore*' ) && !request()->is( 'docstore-*' ) ? 'active' : '' ?>" >
                    <a href="<?= route('docstore-dir@list' ) ?>" class="nav-link">
                        Document Store
                    </a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) && request()->is( 'docstore*' ) ): ?>
                    <li class="nav-sub-menu-item <?= request()->is( 'docstorec*' ) ? 'active' : '' ?>" >
                        <a href="<?= route('docstore-c-dir@customers' ) ?>" class="nav-link">
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Documents
                        </a>
                    </li>
                <?php endif; ?>
            <?php elseif( !config( 'ixp_fe.frontend.disabled.docstore_customer' ) ): ?>
                <li class="<?= request()->is( 'docstorec*' ) ? 'active' : '' ?>" >
                    <a href="<?= route('docstore-c-dir@customers' ) ?>" class="nav-link">
                        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Document Store
                    </a>
                </li>
            <?php endif; ?>

            <?php /**************************************** DOCSTORE ****************************************/ ?>

            <?php if( !config( 'ixp_fe.frontend.disabled.ripe-atlas' ) ): ?>
                <li class="<?= !request()->is( 'ripe-atlas/runs*' ) ?: 'active' ?>" >
                    <a href="<?= route('ripe-atlas/runs@list' ) ?>" class="nav-link">
                        Ripe Atlas
                    </a>
                </li>

                <?php if( request()->is( 'ripe-atlas/*' ) ): ?>
                    <ul>
                        <li class="nav-sub-menu-item <?= !request()->is( 'ripe-atlas/measurements/*' ) ?: 'active' ?>">
                            <a class="nav-link" href="<?= route('ripe-atlas/measurements@list' ) ?>">
                                Measurements
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>

                <?php if( request()->is( 'ripe-atlas/*' ) ): ?>
                    <ul>
                        <li class="nav-sub-menu-item <?= !request()->is( 'ripe-atlas/probes/*' ) ?: 'active' ?>">
                            <a class="nav-link" href="<?= route('ripe-atlas/probes@list' ) ?>">
                                Probes
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>

            <?php endif; ?>

            <li class="<?= !request()->is( 'facility/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route( 'facility@list' ) ?>">
                    Facilities
                </a>
            </li>

            <li class="<?= !request()->is( 'infrastructure/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route('infrastructure@list') ?>">
                    Infrastructures
                </a>
            </li>

            <li>
                <a href="<?= route('ip-address@list', [ 'protocol' => 4 ] ) ?>" class="nav-link">
                    IP Addresses
                </a>
            </li>
            <?php if( request()->is( 'ip-address/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= (int)request()->route()->parameter('protocol') === 4 ? 'active' : '' ?>">
                        <a href="<?= route('ip-address@list', [ 'protocol' => 4 ] ) ?>" class="nav-link">
                            &nbsp;&nbsp;&nbsp;&nbsp;IPv4 Addresses
                        </a>
                    </li>

                    <li class="nav-sub-menu-item <?= (int)request()->route()->parameter('protocol') === 6 ? 'active' : '' ?>">
                        <a href="<?= route('ip-address@list', [ 'protocol' => 6 ] ) ?>" class="nav-link">
                            &nbsp;&nbsp;&nbsp;&nbsp;IPv6 Addresses
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <li class=" <?= !request()->is( 'irrdb-config/*' ) ?: 'active' ?>">
                <a href="<?= route( 'irrdb-config@list' ) ?>" class="nav-link">
                    IRRDB Configuration
                </a>
            </li>

            <li>
                <a href="<?= route( 'layer2-address@list' ) ?>" class="nav-link">
                    MAC Addresses
                </a>
            </li>

            <?php if( request()->is( 'mac-address/*' ) || request()->is( 'layer2-address/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= !request()->is( 'layer2-address/*' ) ?: 'active' ?>">
                        <a href="<?= route( 'layer2-address@list' ) ?>" class="nav-link">
                            Configured Addresses
                        </a>
                    </li>

                    <?php if( !config( 'ixp_fe.frontend.disabled.mac-address', false ) ): ?>
                        <li class="nav-sub-menu-item <?= !request()->is( 'mac-address/*' ) ?: 'active' ?>">
                            <a href="<?= route('mac-address@list') ?>" class="nav-link">
                                Discovered Addresses
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <li class="<?= !request()->is( 'rack/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route('rack@list') ?>">
                    Racks
                </a>
            </li>

            <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                <li class="<?= !request()->is( 'rs-prefixes/*' ) ?: 'active' ?>">
                    <a href="<?= route( 'rs-prefixes@list' ) ?>" class="nav-link">
                        Route Server Prefixes
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?= request()->is( 'router/*' ) && !request()->is( 'router/status' ) ? 'active' : '' ?>" >
                <a href="<?= route('router@list' ) ?>" class="nav-link" >
                    Routers
                </a>
            </li>

            <?php if( request()->is( 'router/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= request()->is( 'router/status' ) ? 'active' : '' ?>" >
                        <a href="<?= route('router@status' ) ?>" class="nav-link" >
                            Live Status
                        </a>
                    </li>
                </ul>
            <?php endif;?>

            <li class="<?= !request()->is( 'switch/*' ) ?: 'active' ?>" >
                <a id="lhs-menu-switches" class="nav-link" href="<?= route('switch@list') ?>">
                    Switches
                </a>
            </li>

            <?php if( request()->is( 'switch/*' ) || request()->is( 'switch-port/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= request()->is( 'switch-port/*' ) && !request()->is( 'switch-port/unused-optics' ) && !request()->is( 'switch-port/optic-inventory' ) && !request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>">
                        <a id="lhs-menu-switch-ports" class="nav-link" href="<?= route( "switch-port@list" ) ?>">
                            Switch Ports
                        </a>
                    </li>
                    <li class="nav-sub-menu-item <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/unused-optics' ) ? 'active' : '' ?>">
                        <a href="<?= route( "switch-port@unused-optics" ) ?>" class="nav-link">
                            Unused Optics
                        </a>
                    </li>
                    <li class="nav-sub-menu-item <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/optic-inventory' )  || request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>">
                        <a href="<?= route( "switch-port@optic-inventory" ) ?>" class="nav-link">
                            Optic Inventory
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <li class="<?= !request()->is( 'vendor/*' ) ?: 'active' ?>" >
                <a href="<?= route('vendor@list' ) ?>" class="nav-link">
                    Vendors
                </a>
            </li>

            <li class="<?= request()->is( 'vlan/*' ) && !request()->is( 'vlan/private' ) ? 'active' : '' ?>">
                <a href="<?= route('vlan@list' ) ?>" class="nav-link">
                    VLANs
                </a>
            </li>

            <?php if( request()->is( 'vlan/*' ) || request()->is( 'network-info/*' ) ): ?>
                <ul>
                    <li class="nav-sub-menu-item <?= !request()->is( 'network-info/*' ) ?: 'active' ?>">
                        <a href="<?= route('network-info@list' ) ?>" class="nav-link">
                            Network Information
                        </a>
                    </li>

                    <li class="nav-sub-menu-item <?= !request()->is( 'vlan/private' ) ?: 'active' ?>">
                        <a href="<?= route( 'vlan@private' ) ?>" class="nav-link">
                            Private VLANs
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <h6>
                <span>IXP STATISTICS</span>
            </h6>

            <li class="<?= !request()->is( 'statistics/members' ) ?: 'active' ?>" >
                <a href="<?= route( 'statistics@members' ) ?>" class="nav-link">
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Statistics
                </a>
            </li>

            <li class="<?= !request()->is( 'statistics/utilisation' ) ?: 'active' ?>">
                <a href="<?= route( 'statistics@utilisation' ) ?>" class="nav-link">
                    Port Utilisation
                </a>
            </li>

            <li class="<?= !request()->is( 'statistics/league-table' ) ?: 'active' ?>">
                <a href="<?= route( 'statistics@league-table' ) ?>" class="nav-link">
                    League Table
                </a>
            </li>

            <h6>
                IXP UTILITIES
            </h6>

            <?php if( Gate::allows( 'viewHorizon' ) && config( 'queue.default' ) === 'redis' ): ?>
                <li>
                    <a href="<?= route( 'horizon.index' ) ?>" class="nav-link" target="_ixpm_horizon">
                        Laravel Horizon
                        <?php if( \IXP\Utils\Horizon::status() === \IXP\Utils\Horizon::STATUS_INACTIVE ): ?>
                            <span class="tw-text-red-500"><i class="fa fa-exclamation-triangle"></i></span>
                        <?php elseif( \IXP\Utils\Horizon::status() === \IXP\Utils\Horizon::STATUS_PAUSED ): ?>
                            <span class="tw-text-orange-500"><i class="fa fa-pause"></i></span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>

            <?php if( Gate::allows( 'viewTelescope' ) && config( 'telescope.enabled' ) ): ?>
                <li>
                    <a href="<?= route( 'telescope' ) ?>" class="nav-link" target="_ixpm_telescope">
                        Laravel Telescope
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?= !request()->is( 'utils/phpinfo' ) ?: 'active' ?>">
                <a href="<?= route( 'utils/phpinfo' ) ?>" class="nav-link">
                    PHP Info
                </a>
            </li>

            <li class="<?= !request()->is( 'utils/ixf-compare' ) ?: 'active' ?>">
                <a href="<?= route( 'utils/ixf-compare' ) ?>" class="nav-link">
                    IX-F Compare
                </a>
            </li>

            <li class="<?= !request()->is( 'login-history/*' ) ?: 'active' ?>">
                <a href="<?= route( 'login-history@list' ) ?>" class="nav-link">
                    Last Logins
                </a>
            </li>

            <?php if( !config( 'ixp_fe.frontend.disabled.logs', false ) ): ?>
                <li class="<?= !request()->is( 'log/*' ) ?: 'active' ?>">
                    <a href="<?= route( 'log@list' ) ?>" class="nav-link">
                        Logs
                    </a>
                </li>
            <?php endif; ?>
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
                                Any other text is searched as <code>%xxx%</code> on <?= config( 'ixp_fe.lang.customer.one' ) ?> details
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