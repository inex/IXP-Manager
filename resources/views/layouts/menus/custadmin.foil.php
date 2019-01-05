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

                <li class="<?= !request()->is( 'contact/*' ) ?: 'active' ?>">
                    <a href="<?= route( 'contact@list' ) ?>">Contacts</a>
                </li>

                <li class="<?= !request()->is( 'user/*' ) ?: 'active' ?>">
                    <a href="<?= route( 'user@list' ) ?>">Users</a>
                </li>

                <?php
                    // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                    // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                    echo $this->insert('header-documentation');
                ?>

                <li class="">
                    <a href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">Support</a>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">My Account<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= route( 'profile@edit' ) ?>">Profile</a>
                        </li>
                        <li>
                            <a href="<?= route('api-key@list' )?>">API Keys</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= route( 'login@logout' ) ?>">Logout</a>
                        </li>
                    </ul>
                </li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>

                    <?php if( session()->exists( "switched_user_from" ) ): ?>
                        <a href="<?= route( 'switch-user@switchBack' ) ?>">Switch Back</a>
                    <?php else: ?>
                        <a href="<?= route( 'login@logout' ) ?>">Logout</a>
                    <?php endif; ?>

                </li>


            </ul>
        </div>
    </div>
</nav>
