<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">

            <!-- Single button -->
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= isset($t->data[ 'params'][ "cs" ]) ? $t->data[ 'params'][ "css" ][$t->data[ 'params'][ "cs" ] ] : "All Console Server Ports" ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                    <li class="<?= isset($t->data[ 'params'][ "cs" ]) ? "" : "active" ?>">
                        <a href="<?= route( "console-server-connection@list" ) ?>">All Console Server Ports</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( $t->data[ 'params'][ "css" ] as $id => $name ): ?>
                        <li class="<?= isset($t->data[ 'params'][ "cs" ]) && $t->data[ 'params'][ "cs" ] === $id ? 'active' : '' ?>">
                            <a href="<?= route( "console-server-connection@listPort", [ "port" => $id ] ) ?>"><?= $name ?></a>
                        </li>


                    <?php endforeach; ?>

                </ul>
            </div>


            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>



        </div>
    </li>
<?php endif;?>