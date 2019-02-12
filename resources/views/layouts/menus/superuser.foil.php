<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="navbar-brand">
        <a class="navbar-brand" href="<?= url('') ?>">
            <?= $this->insert('ixp-logo-header'); ?>

            <?= config('identity.sitename' ) ?>
        </a>

    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Member Information
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?= route('customer@details') ?>">
                        Member Details
                    </a>
                    <a class="dropdown-item" href="<?= route( "customer@associates" ) ?>">
                        Associate Members
                    </a>
                    <a class="dropdown-item" href="<?= url('') ?>/switch/configuration">
                        Switch Configuration
                    </a>
                </div>
            </li>


            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Peering
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if( !config('ixp_fe.frontend.disabled.lg' ) ): ?>
                        <a class="dropdown-item" href="<?= url('lg') ?>">
                            Looking Glass
                        </a>
                    <?php endif; ?>
                    <?php if( !config( 'ixp_fe.frontend.disabled.peering-matrix', false ) ): ?>
                        <a class="dropdown-item" href="<?= route('peering-matrix@index') ?>">
                            Peering Matrix
                        </a>
                    <?php endif; ?>
                </div>
            </li>

            <?=
                // STATIC DOCUMENTATION LINKS - SPECIFIC TO INDIVIDUAL IXPS
                // Add a skinned file in views/_skins/xxx/header-documentation.phtml for your IXP to override the sample
                $this->insert('header-documentation');
            ?>

            <li class="nav-item dropdown <?= !request()->is( 'statistics/*', 'weather-map/*' ) ?: 'active' ?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Statistics
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">

                    <a class="dropdown-item" href="<?= route( 'statistics/ixp' ) ?>">
                        Overall Peering Graphs
                    </a>
                    <a class="dropdown-item" href="<?= route( 'statistics/infrastructure' ) ?>">
                        Infrastructure Graphs
                    </a>

                    <?php if( config( 'grapher.backends.sflow.enabled' ) ): ?>
                        <a class="dropdown-item" href="<?= route( 'statistics/vlan' ) ?>">
                            VLAN / Per-Protocol Graphs
                        </a>
                    <?php endif; ?>

                    <a class="dropdown-item" href="<?= route('statistics/trunk') ?>">
                        Inter-Switch / PoP Graphs
                    </a>
                    <a class="dropdown-item" href="<?= route('statistics/switch') ?>">
                        Switch Aggregate Graphs
                    </a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= route( 'statistics/members' ) ?>">
                        Member Graphs
                    </a>
                    <a class="dropdown-item" href="<?= route( 'statistics/league-table' ) ?>">
                        League Table
                    </a>


                    <?php if( is_array( config( 'ixp_tools.weathermap', false ) ) ): ?>
                        <div class="dropdown-divider"></div>

                        <?php foreach( config( 'ixp_tools.weathermap' ) as $k => $w ): ?>
                            <a class="dropdown-item" href="<?= route( 'weathermap' , [ 'id' => $k ] ) ?>"><?= $w['menu'] ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= route( 'public-content', [ 'page' => 'support' ] ) ?>">
                    Support
                </a>
            </li>


            <?= $this->insert('staff-links'); ?>

        </ul>

        <form class="form-inline my-2 my-lg-0">
            <select id="menu-select-customer" type="select" name="id" class="chzn-select col-sm-7">
                <option></option>
                <?php foreach( $t->customers as $k => $i ): ?>
                    <option value="<?= $k ?>"><?= $i ?></option>
                <?php endforeach; ?>
            </select>
        </form>

        <ul class="navbar-nav mt-lg-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    My Account
                </a>
                <ul class="dropdown-menu dropdown-menu-right">

                    <a class="dropdown-item" href="<?= route( 'profile@edit' ) ?>">Profile</a>

                    <a class="dropdown-item" href="<?= route('api-key@list' )?>">API Keys</a>

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="<?= route( 'customerNotes@unreadNotes' ) ?>">Unread Notes</a>

                    <div class="dropdown-divider"></div>

                    <?php if( session()->exists( "switched_user_from" ) ): ?>
                        <a class="dropdown-item" href="<?= route( 'switch-user@switchBack' ) ?>">Switch Back</a>
                    <?php else: ?>
                        <a class="dropdown-item" href="<?= route( 'login@logout' ) ?>">Logout</a>
                    <?php endif; ?>

                </ul>
            </li>
        </ul>
    </div>
</nav>