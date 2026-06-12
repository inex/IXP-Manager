
<div class="btn-group btn-group-sm ml-auto" role="group">
    
    <?php if( (bool)request()->query( 'all', 0 ) ): ?>
        <a class="btn btn-white" href="<?= route('app-password@list') ?>">
            Mine Only
        </a>
    <?php else: ?>
        <a class="btn btn-white" href="<?= route('app-password@list') ?>?all=1">
            All Users
        </a>
    <?php endif; ?>

    <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
        <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
            Documentation
        </a>
    <?php endif; ?>
    
    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create') ?>">
            <i class="fa fa-plus"></i>
        </a>
    <?php endif;?>
</div>

