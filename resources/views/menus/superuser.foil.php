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
                            <a href="{genUrl controller="switch" action="configuration"}">Switch Configuration</a>
                        </li>
                        <li>
                            <a href="{genUrl controller="customer" action="details"}">Member Details</a>
                        </li>
                        <?php if( !config( 'ixp_fe.frontend.disabled.meeting', true ) ): ?>
                            <li>
                                <a href="{genUrl controller="meeting" action="read"}">Meetings</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Peering<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <?php if( !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                            <li><a href="{genUrl controller="peering-matrix"}">Public Peering Matrix</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php
                    // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                    // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                    echo $this->insert('header-documentation');
                ?>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Statistics<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= url('statistics/public') ?>">Overall Peering Graphs</a>
                        </li>
                        <li>
                            <a href="<?= url('statistics/trunks') ?>">Inter-Switch / PoP Graphs</a>
                        </li>
                        <li>
                            <a href="<?= url('statistics/switches') ?>">Switch Aggregate Graphs</a>
                        </li>
                        <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                            <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                                <li>
                                    <a href="<?= url( '/weather-map/index/id/' . $k ) ?>"><?= $w['menu'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="">
                    <a href="<?= url( '/static/support' ) ?>">Support</a>
                </li>

                <?= $this->insert('staff-links'); ?>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= url('profile') ?>">Profile</a>
                        </li>
                        <li>
                            <a href="<?= url('api-key' )?>">API Keys</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= url( 'customer/unread-notes' ) ?>">Unread Notes</a>
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
                    <select data-placeholder="View a Customer..." id="menu-select-customer" type="select" name="id" class="chzn-select">
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
