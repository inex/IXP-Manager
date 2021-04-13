<div class="btn-group btn-group-sm">
    <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview">
        <i class="fa fa-eye"></i>
    </a>

    <?php if( $t->row[ 'atlas_result_id' ] ): ?>
        <a class="btn btn-white <?= $t->row[ 'atlas_result_id' ] ?: 'disabled' ?>"  href="<?= route( 'ripe-atlas/results@view', [ 'id' => $t->row[ 'atlas_result_id' ] ] ) ?>" title="Show atlas result">
            <i class="fa fa-file-text"></i>
        </a>
    <?php endif; ?>
</div>