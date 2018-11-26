<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <div class="btn-group">

                <?php if( isset($t->data[ 'params'][ "activeOnly" ] ) && $t->data[ 'params'][ "activeOnly" ] ): ?>

                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 0 ] ) ?>">
                        Show Active &amp; Inactive
                    </a>

                <?php else: ?>

                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 1 ] ) ?>">
                        Show Active Only
                    </a>

                <?php endif; ?>

                <div class="btn-group">

                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        List Mode: <?= $t->data[ 'params']['vtype'] ?>&nbsp;<span class="caret"></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                        <li class="<?= $t->data[ 'params']['vtype'] === "Default" ? "active" : "" ?>">
                            <a href="<?= route( "switch@list" , [ "vtype" => "Default" ] ) ?>">Default</a>
                        </li>

                        <li class="<?= $t->data[ 'params']['vtype'] === "OS View" ? "active" : "" ?>">
                            <a href="<?= route( "switch@list" , [ "vtype" => "OS View" ] ) ?>">OS View</a>
                        </li>

                        <li class="<?= $t->data[ 'params']['vtype'] === "L3 View" ? "active" : "" ?>">
                            <a href="<?= route( "switch@list" , [ "vtype" => "L3 View" ] ) ?>">L3 View</a>
                        </li>

                    </ul>

                </div>

                <a type="button" class="btn btn-default btn-xs"  target="_blank" href="https://docs.ixpmanager.org/usage/switches/">
                    Help
                </a>

            </div>

            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add-by-snmp') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        </div>

    </li>
<?php endif;?>

