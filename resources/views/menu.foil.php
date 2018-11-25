<div class="row-fluid clearfix">
    <div class="col-md-2 no-padding">
        <div class="well sidebar-nav left-sidebar">
            <ul class="nav nav-pills nav-stacked ">
                <form class="form-inline sidebar-search-form" method="get" action="<?= route( 'search' ) ?>">
                    <div class="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for..." name="search">
                            <span class="input-group-btn">
                                <a class="btn btn-default " id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">

                                    <span class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            </span>
                        </div><!-- /input-group -->
                    </div>
                </form>

                <li class="nav-header">
                    IXP Customer Actions
                </li>

                <li  <?php if( $t->controller == 'CustomerController' ): ?> class="active" <?php endif; ?> >
                    <a href="<?= route( 'customer@list' ) ?>">Customers</a>
                </li>
                <?php if( $t->controller == 'CustomerController' || $t->controller == 'CustomerTagController' ): ?>
                    <li class="sub-menu <?php if( $t->controller == 'CustomerTagController' ):?> active <?php endif;?> " >
                        <a href="<?= route('customer-tag@list' ) ?>">Tags</a>
                    </li>
                <?php endif; ?>

                <li <?php if( $t->controller == 'VirtualInterfaceController' ): ?> class="active" <?php endif; ?> >

                    <a href="<?= route( 'interfaces/virtual/list' ) ?>" >
                        Interfaces / Ports
                    </a>

                </li>

                    <?php if( substr( $t->controller, -19 ) == 'InterfaceController' ): ?>

                        <li class="sub-menu <?php if( $t->controller == 'PhysicalInterfaceController' ):?> active <?php endif;?> " >
                            <a href="<?= route('interfaces/physical/list' ) ?>">Physical Interface</a>
                        </li>

                        <li class="sub-menu <?php if( $t->controller == 'VlanInterfaceController' ):?> active <?php endif;?> " >
                            <a href="<?= route('interfaces/vlan/list' ) ?>">Vlan Interface</a>
                        </li>

                    <?php endif; ?>

                <li class="<?= $t->controller != 'SflowReceiverController' ?: 'active' ?>" >
                    <a href="<?= route('interfaces/sflow-receiver/list') ?>">Sflow Receivers</a>
                </li>

                <li <?php if( $t->controller == 'PatchPanelController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route('patch-panel/list' ) ?>">Patch Panels</a>
                    <?php if( $t->controller == 'PatchPanelPortController' || $t->controller == 'PatchPanelController' ):?>
                        <li class="sub-menu <?php if( $t->controller == 'PatchPanelPortController' ):?> active <?php endif;?> " >
                            <a href="<?= route('patch-panel-port/list' ) ?>">Patch Panel Port</a>
                        </li>
                    <?php endif;?>
                </li>
                <li>
                    <a href="<?= url( 'user/list' ) ?>">Users</a>
                </li>

                <li>
                    <a href="<?= url( '/contact/list' ) ?>">Contacts</a>
                    <?php /* {if $controller eq 'contact' or $controller eq 'contact-group'}
                        <ul class="nav nav-list">
                            <li {if $controller eq 'contact-group'}class="active"{/if}>
                                <a href="{genUrl controller='contact-group' action='list'}">Contact Groups</a>
                            </li>
                        </ul>
                    {/if} */ ?>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.cust-kit', false ) ): ?>
                    <li <?php if( $t->controller == 'CustKitController' ):?> class="active" <?php endif;?> >
                        <a href="<?= route( 'cust-kit@list' ) ?>">Colocated Equipment</a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Admin Actions
                </li>

                <li <?php if( $t->controller == 'InfrastructureController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route('infrastructure@list') ?>">Infrastructures</a>
                </li>

                <li <?php if( $t->controller == 'LocationController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route( 'facility@list' ) ?>">Facilities</a>
                </li>

                <li <?php if( $t->controller == 'CabinetController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route('rack@list') ?>">Racks</a>
                </li>

                <li <?php if( $t->controller == 'SwitchController' ):?> class="active" <?php endif;?> >
                    <a id="lhs-menu-switches" href="<?= route('switch@list') ?>">Switches</a>

                    <?php if( $t->controller == 'SwitchController' || $t->controller == 'SwitchPortController' ):?>
                        <li class="sub-menu <?php if( $t->controller == 'SwitchPortController' && $t->action != 'unusedOptics' && $t->action != 'opticInventory' && $t->action != 'opticList' ):?> active <?php endif;?>" >
                            <a id="lhs-menu-switch-ports" href="<?= route( "switch-port@list" ) ?>">Switch Ports</a>
                        </li>
                        <li class="sub-menu <?php if( $t->controller == 'SwitchPortController' && $t->action == 'unusedOptics' ):?> active <?php endif;?>" >
                            <a href="<?= route( "switch-port@unused-optics" ) ?>">Unused Optics</a>
                        </li>
                        <li class="sub-menu <?php if( $t->controller == 'SwitchPortController' && $t->action == 'opticInventory' || $t->action == 'opticList' ):?> active <?php endif;?>" >
                            <a href="<?= route( "switch-port@optic-inventory" ) ?>">Optic Inventory</a>
                        </li>
                    <?php endif; ?>
                </li>

                <li <?= $t->controller == 'RouterController' && $t->action != 'status' ? 'class="active"' : '' ?>>
                    <a href="<?= route('router@list' ) ?>">
                        Routers
                    </a>

                    <?php if( $t->controller == 'RouterController' ): ?>
                        <li class="sub-menu <?php if( $t->controller == 'RouterController' && $t->action == 'status'):?> active <?php endif;?> " >
                            <a href="<?= route('router@status' ) ?>">Live Status</a>
                        </li>
                    <?php endif;?>

                </li>

                <li <?php if( $t->controller == 'ConsoleServerController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route('console-server@list' ) ?>">Console Servers </a>

                    <?php if( $t->controller == 'ConsoleServerController' || $t->controller == 'ConsoleServerConnectionController' ):?>
                        <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection', false ) ): ?>
                            <li class="sub-menu <?php if( $t->controller == 'ConsoleServerConnectionController' ):?> active <?php endif;?>" >
                                <a href="<?= route('console-server-connection@list' ) ?>">Console Server Connections</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </li>



                <?php if( config( 'ixp_fe.frontend.beta.core_bundles', false ) ): ?>

                    <li class="<?= $t->controller != 'CoreBundleController' ?: "active" ?>" >
                        <a href="<?= route('core-bundle/list' ) ?>">Core Bundles</a>
                    </li>

                <?php endif; ?>

                <li>
                    <a href="<?= route('ip-address@list', [ 'protocol' => 6 ] ) ?>">IP Addresses</a>
                    <?php if( request()->is( 'ip-address/*' ) ): ?>
                        <li class="sub-menu <?= request()->route()->parameter('protocol') == '4' ? 'active' : '' ?>">
                            <a href="<?= route('ip-address@list', [ 'protocol' => 4 ] ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;IPv4 Addresses</a>
                        </li>

                    <li class="sub-menu <?= request()->route()->parameter('protocol') == '6' ? 'active' : '' ?>">
                            <a href="<?= route('ip-address@list', [ 'protocol' => 6 ] ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;IPv6 Addresses</a>
                        </li>
                    <?php endif; ?>
                </li>

                <li>
                    <a href="<?= route( 'layer2-address@list' ) ?>">MAC Addresses</a>
                </li>

                <?php if(  $t->controller == 'MacAddressController' ||  $t->controller == 'Layer2AddressController' ): ?>
                    <li class="sub-menu <?= $t->controller == 'Layer2AddressController' ? 'active' : '' ?>">
                        <a href="<?= route( 'layer2-address@list' ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;Configured Addresses</a>
                    </li>

                    <?php if( !config( 'ixp_fe.frontend.disabled.mac-address', false ) ): ?>
                        <li class="sub-menu <?= $t->controller == 'MacAddressController' ? 'active' : '' ?>">
                            <a href="<?= route('mac-address@list') ?>">&nbsp;&nbsp;&nbsp;&nbsp;Discovered Addresses</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


                <li <?php if( $t->controller == 'VendorController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route('vendor@list' ) ?>">Vendors</a>
                </li>

                <li <?php if( $t->controller == 'VlanController' && !strpos( strtolower($t->action) , 'private') ):?> class="active" <?php endif;?> >
                    <a href="<?= route('vlan@list' ) ?>">VLANs</a>
                    <?php /* {if $controller eq 'vlan'}
                        <ul class="nav nav-list">
                            <li {if $controller eq 'vlan' and $action eq 'private'}class="active"{/if}>
                                <a href="{genUrl controller='vlan' action='private'}">Private VLANs</a>
                            </li>
                        </ul>
                    {/if} */ ?>
                </li>

                <?php if(  $t->controller == 'VlanController' ): ?>
                    <li class="sub-menu <?= $t->controller == 'VlanController' && strpos( strtolower($t->action) , 'private')  ? 'active' : '' ?>">
                        <a href="<?= route( 'vlan@private' ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;Private VLANs</a>
                    </li>
                <?php endif; ?>

                <li <?php if( $t->controller == 'IrrdbConfigController' ):?> class="active" <?php endif;?> >
                    <a href="<?= route( 'irrdb-config@list' ) ?>">IRRDB Configuration</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                    <li <?php if( request()->is( 'rs-prefixes/*' ) ): ?> class="active" <?php endif; ?>>
                        <a href="<?= route( 'rs-prefixes@list' ) ?>">Route Server Prefixes</a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Statistics
                </li>

                <li>
                    <a href="<?= route( 'statistics/members' ) ?>">Member Statistics</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.logo', true ) ): ?>
                    <li>
                        <a href="<?= route('logo@logos' ) ?>">Member Logos</a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="<?= route( 'statistics/league-table' ) ?>">League Table</a>
                </li>

                <?php /*
                {* 95th Percentiles {genUrl controller="customer" action="ninety-fifth"} *}
                {* Last Logins      {genUrl controller="user" action="last"} *}
                */ ?>

                <li class="nav-header">
                    IXP Utilities
                </li>

                <li <?= !Route::current()->named('utils/phpinfo') ?: 'class="active"' ?>>
                    <a href="<?= route( 'utils/phpinfo' ) ?>">PHP Info</a>
                </li>

                <li>
                    <a href="<?= route( 'login-history@list' ) ?>">Last Logins</a>
                </li>
            </ul>
        </div><!--/.well -->
        <div class="modal fade" id="searchHelpModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Search Help</h4>
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
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--/span-->
<div class="col-md-10">
