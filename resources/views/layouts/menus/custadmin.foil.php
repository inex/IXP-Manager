<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="<?= url('') ?>">
        <?= $this->insert('ixp-logo-header'); ?>
        <?= config('identity.sitename' ) ?>
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

        <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">

            <li class="nav-item <?= !request()->is( 'contact/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route( 'contact@list' ) ?>">
                    Contacts
                </a>
            </li>

            <li class="nav-item <?= !request()->is( 'user/*' ) ?: 'active' ?>">
                <a class="nav-link" href="<?= route( 'user@list' ) ?>">
                    Users
                </a>
            </li>

            <?php
                // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                echo $this->insert('header-documentation');
            ?>

            <li class="nav-item">
                <a class="nav-link" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>
        </ul>
        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My Account
                </a>
                <ul class="dropdown-menu dropdown-menu-right">

                    <a class="dropdown-item" href="<?= route( 'profile@edit' ) ?>">
                        Profile
                    </a>

                    <a class="dropdown-item" href="<?= route('api-key@list' )?>">
                        API Keys
                    </a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?= route( 'login@logout' ) ?>">
                        Logout
                    </a>

                </ul>
            </li>

            <li class="nav-item">
                <?php if( session()->exists( "switched_user_from" ) ): ?>
                    <a class="nav-link" href="<?= route( 'switch-user@switchBack' ) ?>">
                        Switch Back
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="<?= route( 'login@logout' ) ?>">
                        Logout
                    </a>
                <?php endif; ?>
            <li>

        </ul>
    </div>
</nav>
