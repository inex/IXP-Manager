<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>

    <div class="btn-group btn-group-sm" role="group">

        <div class="btn-group btn-group-sm">

            <?php if( isset($t->data[ 'params'][ "activeOnly" ] ) && $t->data[ 'params'][ "activeOnly" ] ): ?>

                <a class="btn btn-outline-secondary" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 0 ] ) ?>">
                    Show Active &amp; Inactive
                </a>

            <?php else: ?>

                <a class="btn btn-outline-secondary" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 1 ] ) ?>">
                    Show Active Only
                </a>

            <?php endif; ?>


            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                List Mode: <?= $t->data[ 'params']['vtype'] ?>
            </button>

            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= $t->data[ 'params']['vtype'] !== "Default" ?: "active" ?>" href="<?= route( "switch@list" , [ "vtype" => "Default" ] ) ?>">Default</a>

                <a class="dropdown-item <?= $t->data[ 'params']['vtype'] !== "OS View" ?: "active" ?>" href="<?= route( "switch@list" , [ "vtype" => "OS View" ] ) ?>">OS View</a>

                <a class="dropdown-item <?= $t->data[ 'params']['vtype'] !== "L3 View" ?: "active" ?>" href="<?= route( "switch@list" , [ "vtype" => "L3 View" ] ) ?>">L3 View</a>
            </div>


            <a class="btn btn-outline-secondary"  target="_blank" href="https://docs.ixpmanager.org/usage/switches/">
                Help
            </a>

        </div>

        <a class="btn btn-outline-secondary" href="<?= route($t->feParams->route_prefix.'@add-by-snmp') ?>">
            <span class="fa fa-plus"></span>
        </a>

    </div>

<?php endif;?>

