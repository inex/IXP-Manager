<div class="btn-group btn-group-sm">
    <a class="btn btn-white" href="<?= route($t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>"  title="Preview">
        <i class="fa fa-eye"></i>
    </a>
    <a class="btn btn-white" href="<?= route('contact@list' ) ?>?cgid=<?= $t->row[ 'id' ] ?> " title="List Contacts">
        <i class="fa fa-user"></i>
    </a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-white" href="<?= route($t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a class="btn btn-white btn-2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ]  )  ?>"  title="Delete">
            <i class="fa fa-trash"></i>
        </a>
    <?php endif;?>
</div>
