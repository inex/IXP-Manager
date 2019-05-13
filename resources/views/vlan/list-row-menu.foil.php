<div class="btn-group btn-group-sm">

    <a class="btn btn-white" href="<?= route( $t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview">
        <i class="fa fa-eye"></i>
    </a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a id="d2f-list-edit-<?=  $t->row[ 'id' ] ?>" class="btn btn-white" href="<?= route( $t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
            <i class="fa fa-pencil"></i>
        </a>

        <button id='d2f-list-delete-dd-<?= $t->row[ 'id' ] ?>' type="button" class="btn btn-white dropdown-toggle d-flex center-dd-caret" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-trash"></i>
        </button>

        <div class="dropdown-menu dropdown-menu-right">

            <a class="dropdown-item d2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete">
                Delete the Vlan
            </a>
            <a class="dropdown-item" href="<?= route( 'ip-address@delete-by-network' , [ 'vlan' => $t->row['id']  ] ) ?>">
                Delete IP Addresses...
            </a>

        </div>

    <?php endif;?>

</div>
