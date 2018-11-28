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

                <li class="">
                    <a href="<?= route( 'contact@list' ) ?>">Contacts</a>
                </li>

                <li class="">
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
                            <a href="<?php url( 'auth/logout' ) ?>">Logout</a>
                        </li>
                    </ul>
                </li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if($t->switched_user_from): ?>
                    <li><a href="<?= url( 'auth/switch-user-back' ) ?>">Switch Back</a></li>
                <?php else: ?>
                    <li><a href="<?= url( 'auth/logout' ) ?>">Logout </a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
