<div class="btn-group">

    <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?><?= isset( $t->data[ 'params'][ "cs" ] ) ? "?console-server=" . $t->data[ 'params'][ "cs" ] : ""  ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
        <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
    <?php endif;?>

</div>