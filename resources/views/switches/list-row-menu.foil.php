<div class="btn-group">

    <a class="btn btn-sm btn-default" id="d2f-list-view-<?= $t->row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
    <a class="btn btn-sm btn-default" id="d2f-list-edit-<?= $t->row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
    <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>"  title="Delete"><i class="glyphicon glyphicon-trash"></i></a>

    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">

        <li class="dropdown-header">SNMP Actions</li>

        <li <?php if( !$t->row[ "active" ] ): ?> class="disabled" <?php endif; ?> >
            <a href="<?= route( "switch-port@snmp-poll", [ "switch" => $t->row[ 'id' ] ] ) ?>">View / Edit Ports</a>
        </li>

        <li <?php if( !$t->row[ "active" ] ): ?> class="disabled" <?php endif; ?> >
            <a href="<?= route( "switch-port@list-op-status", [ "switch" => $t->row[ 'id' ] ] ) ?>">Live Port States</a>
        </li>

        <li role="separator" class="divider"></li>
        <li class="dropdown-header">Database Actions</li>

        <li>
            <a href="<?= route( "switch-port@list", [ "switch" => $t->row[ 'id' ] ] ) ?>">View / Edit Ports</a>
        </li>

        <li <?php if( !$t->row[ "mauSupported" ] ): ?> class="disabled" <?php endif; ?> >
            <a href="<?= route( "switch-port@list-mau", [ "switch" => $t->row[ 'id' ] ] ) ?> ">Port MAU Detail</a>
        </li>

        <li>
            <a href="<?= route( "switch@port-report", [ "id" => $t->row[ 'id' ] ] ) ?>">Port Report</a>
        </li>

    </ul>

</div>