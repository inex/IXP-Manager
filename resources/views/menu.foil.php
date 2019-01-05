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

                <li class="<?= !request()->is( 'customer/*' ) ?: 'active' ?>" >
                    <a href="<?= route( 'customer@list' ) ?>">Customers</a>
                </li>
                <?php if( request()->is( 'customer/*' ) || request()->is( 'customer-tag/*' ) ): ?>

                    <li class="sub-menu <?= !request()->is( 'customer-tag/*' ) ?: 'active' ?> " >
                        <a href="<?= route('customer-tag@list' ) ?>">Tags</a>
                    </li>
                <?php endif; ?>

                <li class="<?= !request()->is( 'interfaces/virtual*' ) ?: 'active' ?>" >

                    <a href="<?= route( 'interfaces/virtual/list' ) ?>" >
                        Interfaces / Ports
                    </a>

                </li>
                    <?php if( request()->is( 'interfaces/*' ) ): ?>

                        <li class="sub-menu <?= !request()->is( 'interfaces/physical/*' ) ?: 'active' ?> " >
                            <a href="<?= route('interfaces/physical/list' ) ?>">Physical Interface</a>
                        </li>

                        <li class="sub-menu <?= !request()->is( 'interfaces/vlan/*' ) ?: 'active' ?> " >
                            <a href="<?= route('interfaces/vlan/list' ) ?>">Vlan Interface</a>
                        </li>

                    <?php endif; ?>

                <li class="<?= !request()->is( 'interfaces/sflow-receiver/*' ) ?: 'active' ?>" >
                    <a href="<?= route('interfaces/sflow-receiver/list') ?>">Sflow Receivers</a>
                </li>

                <li class="<?= !request()->is( 'patch-panel/*' ) ?: 'active' ?>" >

                    <a href="<?= route('patch-panel/list' ) ?>">Patch Panels</a>

                    <?php if( request()->is( 'patch-panel/*' ) || request()->is( 'patch-panel-port/*' ) ): ?>

                        <li class="sub-menu <?= !request()->is( 'patch-panel-port/*' ) ?: 'active' ?> " >
                            <a href="<?= route('patch-panel-port/list' ) ?>">Patch Panel Port</a>
                        </li>
                    <?php endif;?>

                </li>

                <li class="<?= !request()->is( 'user/*' ) ?: 'active' ?> " >
                    <a href="<?= route('user@list') ?>">Users</a>
                </li>

                <li class="<?= !request()->is( 'contact/*' ) ?: 'active' ?> " >

                    <a href="<?= route( 'contact@list' ) ?>">Contacts</a>

                    <?php if( request()->is( 'contact/*' ) || request()->is( 'contact-group/*' ) ): ?>
                        <li class="sub-menu <?= !request()->is( 'contact-group/*' ) ?: 'active' ?> " >
                            <a href="<?= route('contact-group@list' ) ?>">Contact Groups</a>
                        </li>
                    <?php endif;?>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.cust-kit', false ) ): ?>
                    <li class="<?= !request()->is( 'cust-kit/*' ) ?: 'active' ?>" >
                        <a href="<?= route( 'cust-kit@list' ) ?>">Colocated Equipment</a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Admin Actions
                </li>

                <li class="<?= !request()->is( 'infrastructure/*' ) ?: 'active' ?>" >
                    <a href="<?= route('infrastructure@list') ?>">Infrastructures</a>
                </li>

                <li class="<?= !request()->is( 'facility/*' ) ?: 'active' ?>" >
                    <a href="<?= route( 'facility@list' ) ?>">Facilities</a>
                </li>

                <li class="<?= !request()->is( 'rack/*' ) ?: 'active' ?>" >
                    <a href="<?= route('rack@list') ?>">Racks</a>
                </li>

                <li class="<?= !request()->is( 'switch/*' ) ?: 'active' ?>" >
                    <a id="lhs-menu-switches" href="<?= route('switch@list') ?>">Switches</a>

                    <?php if( request()->is( 'switch/*' ) || request()->is( 'switch-port/*' ) ): ?>
                        <li class="sub-menu <?= request()->is( 'switch-port/*' ) && !request()->is( 'switch-port/unused-optics' ) && !request()->is( 'switch-port/optic-inventory' ) && !request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>" >
                            <a id="lhs-menu-switch-ports" href="<?= route( "switch-port@list" ) ?>">Switch Ports</a>
                        </li>
                        <li class="sub-menu <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/unused-optics' ) ? 'active' : '' ?>" >
                            <a href="<?= route( "switch-port@unused-optics" ) ?>">Unused Optics</a>
                        </li>
                        <li class="sub-menu <?= request()->is( 'switch-port/*' ) && request()->is( 'switch-port/optic-inventory' )  || request()->is( 'switch-port/optic-list' ) ? 'active' : '' ?>" >
                            <a href="<?= route( "switch-port@optic-inventory" ) ?>">Optic Inventory</a>
                        </li>
                    <?php endif; ?>
                </li>

                <li class="<?= request()->is( 'router/*' ) && !request()->is( 'router/status' ) ? 'active' : '' ?>" >
                    <a href="<?= route('router@list' ) ?>">
                        Routers
                    </a>

                    <?php if( request()->is( 'router/*' ) ): ?>
                        <li class="sub-menu <?= request()->is( 'router/*' ) && request()->is( 'router/status' ) ? 'active' : '' ?> " >
                            <a href="<?= route('router@status' ) ?>">Live Status</a>
                        </li>
                    <?php endif;?>

                </li>

                <li class="<?= !request()->is( 'console-server/*' ) ?: 'active' ?>" >
                    <a href="<?= route('console-server@list' ) ?>">Console Servers </a>

                    <?php if( request()->is( 'console-server/*' ) || request()->is( 'console-server-connection/*' ) ): ?>
                        <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection', false ) ): ?>
                            <li class="sub-menu <?= !request()->is( 'console-server-connection/*' ) ?: 'active' ?>" >
                                <a href="<?= route('console-server-connection@list' ) ?>">Console Server Connections</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                </li>



                <?php if( config( 'ixp_fe.frontend.beta.core_bundles', false ) ): ?>

                    <li class="<?= !request()->is( 'interfaces/core-bundle/*' ) ?: 'active' ?>" >
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
                <?php if( request()->is( 'mac-address/*' ) || request()->is( 'layer2-address/*' ) ): ?>

                    <li class="sub-menu <?= !request()->is( 'layer2-address/*' ) ?: 'active' ?> ">
                        <a href="<?= route( 'layer2-address@list' ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;Configured Addresses</a>
                    </li>

                    <?php if( !config( 'ixp_fe.frontend.disabled.mac-address', false ) ): ?>

                        <li class="sub-menu <?= !request()->is( 'mac-address/*' ) ?: 'active' ?>">
                            <a href="<?= route('mac-address@list') ?>">&nbsp;&nbsp;&nbsp;&nbsp;Discovered Addresses</a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>


                <li class="<?= !request()->is( 'vendor/*' ) ?: 'active' ?>" >
                    <a href="<?= route('vendor@list' ) ?>">Vendors</a>
                </li>


                <li class="<?= request()->is( 'vlan/*' ) && !request()->is( 'vlan/private' ) ? 'active' : '' ?>" >
                    <a href="<?= route('vlan@list' ) ?>">VLANs</a>
                </li>

                <?php if(  request()->is( 'vlan/*' ) || $t->controller == 'NetworkInfoController' ): ?>

                    <li class="sub-menu <?= !request()->is( 'network-info/*' ) ?: 'active' ?>" >
                        <a href="<?= route('network-info@list' ) ?>">Network Information</a>
                    </li>

                    <li class="sub-menu <?= $t->controller == 'VlanController' && strpos( strtolower($t->action) , 'private')  ? 'active' : '' ?>">
                        <a href="<?= route( 'vlan@private' ) ?>">Private VLANs</a>
                    </li>

                <?php endif; ?>

                <li class="<?= !request()->is( 'irrdb-config/*' ) ?: 'active' ?>" >
                    <a href="<?= route( 'irrdb-config@list' ) ?>">IRRDB Configuration</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                    <li class="<?= !request()->is( 'rs-prefixes/*' ) ?: 'active' ?>" >
                        <a href="<?= route( 'rs-prefixes@list' ) ?>">Route Server Prefixes</a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Statistics
                </li>

                <li class="<?= !request()->is( 'statistics/members' ) ?: 'active' ?>" >
                    <a href="<?= route( 'statistics/members' ) ?>">Member Statistics</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.logo', true ) ): ?>
                    <li class="<?= !request()->is( 'customer-logo/logos' ) ?: 'active' ?>">
                        <a href="<?= route('logo@logos' ) ?>">Member Logos</a>
                    </li>
                <?php endif; ?>

                <li class="<?= !request()->is( 'statistics/league-table' ) ?: 'active' ?> ">
                    <a href="<?= route( 'statistics/league-table' ) ?>">League Table</a>
                </li>

                <?php /*
                {* 95th Percentiles {genUrl controller="customer" action="ninety-fifth"} *}
                {* Last Logins      {genUrl controller="user" action="last"} *}
                */ ?>

                <li class="nav-header">
                    IXP Utilities
                </li>

                <li class="<?= !request()->is( 'utils/phpinfo' ) ?: 'active' ?>" >
                    <a href="<?= route( 'utils/phpinfo' ) ?>">PHP Info</a>
                </li>

                <li class="<?= !request()->is( 'login-history/*' ) ?: 'active' ?>" >
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
