<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>

    <div class="btn-group btn-group-sm" role="group">

        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= isset($t->data[ 'params'][ "cs" ]) ? $t->data[ 'params'][ "css" ][$t->data[ 'params'][ "cs" ] ] : "All Console Server Ports" ?> <span class="caret"></span>
            </button>
            <div class="dropdown-menu dropdown-menu-right scrollable-dropdown">
                <a class="dropdown-item <?= isset($t->data[ 'params'][ "cs" ]) ? "" : "active" ?>" href="<?= route( "console-server-connection@list" ) ?>">
                    All Console Server Ports
                </a>

                <div class="dropdown-divider"></div>

                <?php foreach( $t->data[ 'params'][ "css" ] as $id => $name ): ?>
                    <a class="dropdown-item <?= isset($t->data[ 'params'][ "cs" ]) && $t->data[ 'params'][ "cs" ] === $id ? 'active' : '' ?>" href="<?= route( "console-server-connection@listPort", [ "port" => $id ] ) ?>"><?= $name ?></a>
                <?php endforeach; ?>

            </div>
        </div>


        <a  class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@add' ) ?><?= isset( $t->data[ 'params'][ "cs" ] ) ? "?serverid=" . $t->data[ 'params'][ "cs" ] : ""  ?>">
            <i class="fa fa-plus"></i>
        </a>

    </div>

<?php endif;?>