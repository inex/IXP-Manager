<div class="btn-group">

    <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
    <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
    <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>"  title="Delete"><i class="glyphicon glyphicon-trash"></i></a>

    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
         <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">

        <?php if( $t->row[ "active" ] ): ?>
            <li>
                <a href="<?= route( "switch-ports@snmp-poll", [ "switch" => $t->row[ 'id' ] ] ) ?>">View / Edit Ports (with SNMP poll)</a>
            </li>
            <li>
                <a href="<?= route( "switch-ports@list-op-status", [ "switch" => $t->row[ 'id' ] ] ) ?>">View Live Port States (with SNMP poll)</a>
            </li>
        <?php endif; ?>
            <li>
                <a href="<?= route( "switch-ports@list", [ "switch" => $t->row[ 'id' ] ] ) ?>">View / Edit Ports (database only)</a>
            </li>
        <?php if( $t->row[ "mauSupported" ] ): ?>
            <li>
                <a href="<?= route( "switch-ports@list-mau", [ "switch" => $t->row[ 'id' ] ] ) ?> ">View Port MAU Detail (database only)</a>
            </li>
        <?php endif; ?>

        <li>
            <a href="<?= route( "switch@port-report", [ "id" => $t->row[ 'id' ] ] ) ?>">View Port Report</a>
        </li>

    </ul>

</div>