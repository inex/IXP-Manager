<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <div class="btn-group">

                <?php if( isset($t->data[ 'params'][ "activeOnly" ] ) && $t->data[ 'params'][ "activeOnly" ] ): ?>

                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 0 ] ) ?>">
                        Include Inactive
                    </a>

                <?php else: ?>

                    <a class="btn btn-default btn-xs" href="<?= route( $t->feParams->route_prefix."@list" , [ "active-only" => 1 ] ) ?>">
                        Show Active
                    </a>

                <?php endif; ?>

                <?php if( isset($t->data[ 'params'][ "osView" ] ) && $t->data[ 'params'][ "osView" ] == true ): ?>

                    <a class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@list', [ "os-view" => false ] ) ?>">Standard View</a>

                <?php else: ?>

                    <a class="btn btn-default btn-xs" href="<?= route($t->feParams->route_prefix.'@list' , [ "os-view" => true ] ) ?>">OS View</a>

                <?php endif; ?>

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

