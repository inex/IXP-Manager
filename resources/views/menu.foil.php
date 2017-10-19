<div class="row-fluid clearfix">
    <div class="col-md-2 no-padding">
        <div class="well sidebar-nav left-sidebar">
            <ul class="nav nav-pills nav-stacked ">
                <form class="form-inline sidebar-search-form" method="get" action="<?= url( 'search/do' ) ?>">
                    <input type="text" class="form-control menu-search-input" style="width: 65%" placeholder="Search..." name="search">
                    <a class="btn btn-default menu-search-help-btn " id="searchHelp" data-toggle="modal" data-target="#searchHelpModal">
                        <span class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </form>

                <li class="nav-header">
                    IXP Customer Actions
                </li>

                <li>
                    <a href="<?= url( 'customer/list' ) ?>">Customers</a>
                </li>

                <li <?php if( $t->controller == 'VirtualInterfaceController' ): ?> class="active" <?php endif; ?> >

                    <a href="<?= route( 'interfaces/virtual/list' ) ?>" >
                        Interfaces / Ports
                    </a>

                </li>

                    <?php if( substr( $t->controller, -19 ) == 'InterfaceController' ): ?>

                        <li class="sub-menu <?php if($t->controller == 'PhysicalInterfaceController'):?> active <?php endif;?> " >
                            <a href="<?= route('interfaces/physical/list') ?>">Physical Interface</a>
                        </li>

                        <li class="sub-menu <?php if($t->controller == 'VlanInterfaceController'):?> active <?php endif;?> " >
                            <a href="<?= route('interfaces/vlan/list') ?>">Vlan Interface</a>
                        </li>

                    <?php endif; ?>

                <li class="<?= $t->controller != 'SflowReceiverController' ?: 'active' ?>" >
                    <a href="<?= url('interfaces/sflow-receiver/list') ?>">Sflow Receivers</a>
                </li>

                <li <?php if( $t->controller == 'PatchPanelController' ):?> class="active" <?php endif;?> >
                    <a href="<?= url('patch-panel/list') ?>">Patch Panels</a>
                    <?php if( $t->controller == 'PatchPanelPortController' || $t->controller == 'PatchPanelController' ):?>
                        <li class="sub-menu <?php if($t->controller == 'PatchPanelPortController'):?> active <?php endif;?> " >
                            <a href="<?= url('patch-panel-port/list') ?>">Patch Panel Port</a>
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
                        <a href="<?= action( 'CustKitController@list' ) ?>">Colocated Equipment</a>
                    </li>
                <?php endif; ?>

                <?php if( !config( 'ixp_fe.frontend.disabled.meeting', false ) ): ?>
                    <li>
                        <a href="<?= url( 'meeting/list' ) ?>">Meetings</a>

                        <?php /* {if $controller eq 'meeting' or $controller eq 'meeting-item' or ( $controller eq 'static' and $action eq 'meetings-instructions' )}
                            <ul class="nav nav-list">
                                <li {if $controller eq 'meeting-item'}class="active"{/if}>
                                    <a href="{genUrl controller='meeting-item' action='list'}">Presentations</a>
                                </li>
                                <li {if $controller eq 'meeting' and $action eq 'read'}class="active"{/if}>
                                    <a href="{genUrl controller='meeting' action='read'}">Member View</a>
                                </li>
                                <li {if $controller eq 'static' and $action eq 'meetings-instructions'}class="active"{/if}>
                                    <a href="{genUrl controller='static' action='meetings-instructions'}">Instructions</a>
                                </li>
                            </ul> */ ?>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Admin Actions
                </li>

                <li <?php if( $t->controller == 'InfrastructureController' ):?> class="active" <?php endif;?> >
                    <a href="<?= action('InfrastructureController@list') ?>">Infrastructures</a>
                </li>

                <li <?php if( $t->controller == 'LocationController' ):?> class="active" <?php endif;?> >
                    <a href="<?= action( 'LocationController@list' ) ?>">Locations</a>
                </li>

                <li <?php if( $t->controller == 'CabinetController' ):?> class="active" <?php endif;?> >
                    <a href="<?= action('CabinetController@list') ?>">Cabinets</a>
                </li>

                <li <?= $t->controller == 'RouterController' && $t->action != 'status' ? 'class="active"' : '' ?>>
                    <a href="<?= url('/router/list') ?>">
                        Routers
                    </a>

                    <?php if( $t->controller == 'RouterController' ): ?>
                        <li class="sub-menu <?php if( $t->controller == 'RouterController' && $t->action == 'status'):?> active <?php endif;?> " >
                            <a href="<?= route('router/status') ?>">Live Status</a>
                        </li>
                    <?php endif;?>

                </li>


                <li>
                    <a href="<?= url('/switch/list') ?>">Switches</a>
                    <?php /*
                    {if $controller eq 'switch' or $controller eq 'switch-port'}
                        <ul class="nav nav-list">
                            <li {if $controller eq 'switch-port' and $action neq 'unused-optics'}class="active"{/if}>
                                <a href="{genUrl controller='switch-port' action='list'}">Switch Ports</a>
                            </li>
                            <li {if $controller eq 'switch-port' and $action eq 'unused-optics'}class="active"{/if}>
                                <a href="{genUrl controller='switch-port' action='unused-optics'}">Unused Optics</a>
                            </li>
                        </ul>
                    {/if}
                    */ ?>
                </li>


                <?php if( config( 'ixp_fe.frontend.beta.core_bundles', false ) ): ?>

                    <li class="<?= $t->controller != 'CoreBundleController' ?: "active" ?>" >
                        <a href="<?= url('interfaces/core-bundle/list') ?>">Core Bundles</a>
                    </li>

                <?php endif; ?>

                <li>
                    <a href="<?= url('/ipv6-address/list') ?>">IP Addressing</a>
                    <?php /*{if $controller eq 'ipv4-address' or $controller eq 'ipv6-address'}
                        <ul class="nav nav-list">
                            <li {if $controller eq 'ipv4-address' and $action neq 'add-addresses'}class="active"{/if}>
                                <a href="{genUrl controller='ipv4-address' action='list'}">IPv4 Addresses</a>
                            </li>
                            <li {if $controller eq 'ipv6-address'}class="active"{/if}>
                                <a href="{genUrl controller='ipv6-address' action='list'}">IPv6 Addresses</a>
                            </li>
                        </ul> */ ?>
                </li>

                <li>
                    <a href="<?= url( '/layer2-address/list' ) ?>">MAC Addresses</a>
                </li>

                <?php if(  $t->controller == 'MacAddressController' ||  $t->controller == 'Layer2AddressController' ): ?>
                    <li class="sub-menu <?= $t->controller == 'Layer2AddressController' ? 'active' : '' ?>">
                        <a href="<?= url( '/layer2-address/list' ) ?>">&nbsp;&nbsp;&nbsp;&nbsp;Configured Addresses</a>
                    </li>

                    <?php if( !config( 'ixp_fe.frontend.disabled.mac-address', false ) ): ?>
                        <li class="sub-menu <?= $t->controller == 'MacAddressController' ? 'active' : '' ?>">
                            <a href="<?= action('MacAddressController@list') ?>">&nbsp;&nbsp;&nbsp;&nbsp;Discovered Addresses</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


                <li <?php if( $t->controller == 'VendorController' ):?> class="active" <?php endif;?> >
                    <a href="<?= action('VendorController@list' ) ?>">Vendors</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.console-server-connection', false ) ): ?>
                    <li <?php if( $t->controller == 'ConsoleServerConnectionController' ):?> class="active" <?php endif;?>>
                        <a href="<?= action('ConsoleServerConnectionController@list') ?>">Console Server Connections</a>
                    </li>
                <?php endif; ?>

                <li <?php if( $t->controller == 'VlanController' && !strpos( strtolower($t->action) , 'private') ):?> class="active" <?php endif;?> >
                    <a href="<?= action('VlanController@list' ) ?>">VLANs</a>
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
                    <a href="<?= action( 'IrrdbConfigController@list' ) ?>">IRRDB Configuration</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.rs-prefixes', false ) ): ?>
                    <li>
                        <a href="<?= url( 'rs-prefixes/index' ) ?>">Route Server Prefixes</a>
                    </li>
                <?php endif; ?>

                <li class="nav-header">
                    IXP Statistics
                </li>

                <li>
                    <a href="<?= url( 'statistics/members' ) ?>">Member Statistics - Graphs</a>
                </li>
                <li>
                    <a href="<?= url( 'statistics/list' ) ?>">Member Statistics - List</a>
                </li>

                <?php if( !config( 'ixp_fe.frontend.disabled.logo', true ) ): ?>
                    <li>
                        <a href="<?= url('customer/logos' ) ?>">Member Logos</a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="<?= url( 'statistics/league-table' ) ?>">League Table</a>
                </li>

                <?php /*
                {* 95th Percentiles {genUrl controller="customer" action="ninety-fifth"} *}
                {* Last Logins      {genUrl controller="user" action="last"} *}
                */ ?>

                <li class="nav-header">
                    IXP Utilities
                </li>

                <li>
                    <a href="<?= url( 'utils/phpinfo' ) ?>">PHP Info</a>
                </li>

                <li>
                    <a href="<?= url( 'user/last' ) ?>">Last Logins</a>
                </li>
            </ul>
        </div><!--/.well -->

        <div id="searchHelpModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="searchHelpModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="searchHelpModalLabel">Search Help</h3>
            </div>
            <div class="modal-body">
                <p>
                    The search box allows for an efficient database search via a number of parameters:
                </p>
                <div class="row-fluid">
                    <div class="span6">
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
                                Enter ID <code>PPP-xxx</code>
                            </dd>
                        </dl>
                    </div>
                    <div class="span6">
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
                <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div><!--/span-->
<div class="col-md-10">
