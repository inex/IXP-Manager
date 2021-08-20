<div class="btn-group btn-group-sm">

    <a class="btn btn-white" id="d2f-list-view-<?= $t->row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview">
        <i class="fa fa-eye"></i>
    </a>
    <a class="btn btn-white" id="d2f-list-edit-<?= $t->row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Edit">
        <i class="fa fa-pencil"></i>
    </a>
    <a class="btn btn-white btn-2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $t->row[ 'id' ] ]  )  ?>"  title="Delete">
        <i class="fa fa-trash"></i>
    </a>
    <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
    <div class="dropdown-menu dropdown-menu-right">
        <h6 class="dropdown-header">
            SNMP Actions
        </h6>
        <a class="dropdown-item <?php if( !$t->row[ "active" ] ): ?> disabled <?php endif; ?>" href="<?= route( "switch-port@snmp-poll", [ "switch" => $t->row[ 'id' ] ] ) ?>">
            View / Edit Ports
        </a>

        <a class="dropdown-item <?php if( !$t->row[ "active" ] ): ?> disabled <?php endif; ?>" href="<?= route( "switch-port@list-op-status", [ "switch" => $t->row[ 'id' ] ] ) ?>">
            Live Port States
        </a>
        <div class="dropdown-divider"></div>
        <h6 class="dropdown-header">
            Database Actions
        </h6>
        <a class="dropdown-item" href="<?= route( "switch-port@list", [ "switchid" => $t->row[ 'id' ] ] ) ?>">
            View / Edit Ports
        </a>
        <a class="dropdown-item <?php if( !$t->row[ "mauSupported" ] ): ?> disabled <?php endif; ?>" href="<?= route( "switch-port@list-mau", [ "switch" => $t->row[ 'id' ] ] ) ?> ">
            Port MAU Detail
        </a>
        <a class="dropdown-item" href="<?= route( "switch@port-report", [ "switch" => $t->row[ 'id' ] ] ) ?>">
            Port Report
        </a>
    </div>
</div>