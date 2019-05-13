

<?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>

    <div class="btn-group btn-group-sm ml-auto" role="group">

        <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
            <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                Documentation
            </a>
        <?php endif; ?>

        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <a id="add-user" class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@add-wizard') ?>">
                <i class="fa fa-plus"></i>
            </a>
        <?php endif;?>

    </div>

<?php endif;?>
