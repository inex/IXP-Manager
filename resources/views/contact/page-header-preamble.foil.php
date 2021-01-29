<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
    <div class="btn-group btn-group-sm" role="group">
        <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
            <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                Documentation
            </a>
        <?php endif; ?>

        <?php if( config('contact_group.types.ROLE') ): ?>
            <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= isset( $t->data[ 'params'][ "role" ] ) ? $t->data[ 'params'][ "roles" ][ $t->data[ 'params'][ "role" ] ][ 'name']  : "All Roles" ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item <?= isset( $t->data[ 'params'][ "role" ]) ?: "active" ?>" href="<?= route( $t->feParams->route_prefix . "@list" ) ?>">
                    All Roles
                </a>

                <div class="dropdown-divider"></div>

                <?php foreach( $t->data[ 'params'][ "roles" ] as $index => $role ): ?>
                    <a class="dropdown-item <?= isset($t->data[ 'params'][ "role" ]) && (int)$t->data[ 'params'][ "role" ] === $role[ 'id'] ? 'active' : '' ?>" href="<?= route( $t->feParams->route_prefix . "@list" ) ?>?role=<?= $role[ 'id' ] ?>">
                        <?= $role[ 'name' ] ?>
                    </a>
                <?php endforeach; ?>
            </ul>
        <?php endif;?>

        <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create' ) ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>
<?php endif;?>