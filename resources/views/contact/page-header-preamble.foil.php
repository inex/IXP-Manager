<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <li class="pull-right" style=<?= Auth::getUser()->isSuperUser() ? "margin-top: 10px" : "" ?> >
        <div class="btn-group btn-group-xs" role="group">

            <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
                <a type="button" target="_blank" class="btn btn-default" href="<?= $t->feParams->documentation ?>">Documentation</a>
            <?php endif; ?>

            <?php if( config('contact_group.types.ROLE') ): ?>
                <!-- Single button -->
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= isset( $t->data[ 'params'][ "role" ] ) ? $t->data[ 'params'][ "roles" ][ $t->data[ 'params'][ "role" ] ][ 'name']  : "All Roles" ?> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right scrollable-dropdown">

                        <li class="<?= isset( $t->data[ 'params'][ "role" ]) ? "" : "active" ?>">
                            <a href="<?= route( $t->feParams->route_prefix . "@list" ) ?>">All Roles</a>
                        </li>

                        <li role="separator" class="divider"></li>

                        <?php foreach( $t->data[ 'params'][ "roles" ] as $index => $role ): ?>

                            <li class="<?= isset($t->data[ 'params'][ "role" ]) && $t->data[ 'params'][ "role" ] === $role[ 'id'] ? 'active' : '' ?>">
                                <a href="<?= route( $t->feParams->route_prefix . "@list" ) ?>?role=<?= $role[ 'id' ] ?>"><?= $role[ 'name' ] ?></a>
                            </li>

                        <?php endforeach; ?>

                    </ul>
                </div>
            <?php endif;?>

            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add' ) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>

        </div>
    </li>
<?php endif;?>