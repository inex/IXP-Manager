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

                <?php
                    // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                    // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                    echo $this->insert('header-documentation');
                ?>

                <li class="">
                    <a href="<?= url( '/static/support' ) ?>">Support</a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="<?= url( 'auth/logout' ) ?>">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
