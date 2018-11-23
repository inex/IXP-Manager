<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="<?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>container-fluid<?php else: ?>container<?php endif; ?>">
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
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Member Information <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= route('customer@details') ?>">Member Details</a>
                        </li>
                        <li>
                            <a href="<?= route( "customer@associates" ) ?>">Associate Members</a>
                        </li>
                        <li>
                            <a href="<?= url('') ?>/switch/configuration">Switch Configuration</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                            <li><a href="<?= url('lg') ?>">Looking Glass</a></li>
                        <?php endif; ?>
                        <?php if( !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                            <li><a href="<?= route('peering-matrix@index') ?>">Peering Matrix</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php
                    // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                    // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                    echo $this->insert('header-documentation');
                ?>

                <li class="dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= route( 'statistics/ixp' ) ?>">Overall Peering Graphs</a>
                        </li>
                        <li>
                            <a href="<?= route( 'statistics/infrastructure' ) ?>">Infrastructure Graphs</a>
                        </li>
                        <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                            <li>
                                <a href="<?= route( 'statistics/vlan' ) ?>">VLAN / Per-Protocol Graphs</a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?= route('statistics/trunk') ?>">Inter-Switch / PoP Graphs</a>
                        </li>
                        <li>
                            <a href="<?= route('statistics/switch') ?>">Switch Aggregate Graphs</a>
                        </li>


                        <li class="divider"></li>

                        <li>
                            <a href="<?= route( 'statistics/members' ) ?>">Member Graphs</a>
                        </li>

                        <li>
                            <a href="<?= route( 'statistics/league-table' ) ?>">League Table</a>
                        </li>

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

                <?= $this->insert('staff-links'); ?>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account<b class="caret"></b></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a href="<?= route( 'profile@edit' ) ?>">Profile</a>
                        </li>
                        <li>
                            <a href="<?= route('api-key@list' )?>">API Keys</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= route( 'customerNotes@unreadNotes' ) ?>">Unread Notes</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= url( 'auth/logout' ) ?>">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <form class="navbar-form navbar-search navbar-right">
                <div class="form-group">
                    <select id="menu-select-customer" type="select" name="id" class="chzn-select col-sm-7">
                        <option></option>
                        <?php foreach( $t->customers as $k => $i ): ?>
                            <option value="<?= $k ?>"><?= $i ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
</nav>
