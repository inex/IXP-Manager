<div class="btn-group btn-group-sm">
    <a class="btn btn-white" href="<?= route($t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview">
        <i class="fa fa-eye"></i>
    </a>
    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a id="e2f-list-edit-<?= $t->row[ 'id' ] ?>" class="btn btn-white" href="<?= route($t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a class="btn btn-white btn-2f-list-delete" id='e2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ]  )  ?>"  title="Delete">
            <i class="fa fa-trash"></i>
        </a>
    <?php endif;?>

    <button class="btn btn-white dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
    <ul class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="<?= route( 'switch@list' ) . "?infra=" . $t->row['id']?>">
            View Switches
        </a>
        <a class="dropdown-item" href="<?= route( 'vlan@infra' ,          [ 'infra' => $t->row['id'] ]   )?>">
            View All VLANs
        </a>
        <a class="dropdown-item" href="<?= route( "vlan@infraPublic",     [ 'infra' => $t->row['id'], 'public' => 1 ]   )?>">
            View Public VLANs
        </a>
        <a class="dropdown-item" href="<?= route( "vlan@privateInfra",    [ 'infra' => $t->row['id'] ]   )?>">
            View Private VLANs
        </a>
    </ul>
</div>