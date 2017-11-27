<div class="btn-group">

    <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>




        <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="glyphicon glyphicon-trash"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
                <a id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete">Delete the Vlan</a>
                <a href="<?= route( 'ip-address@pre-delete-for-vlan' , [ 'vlan' => $t->row['id']                    ] ) ?>">Delete free IP addresses in VLAN</a>
                <a href="<?= route( 'ip-address@pre-delete-for-vlan' , [ 'vlan' => $t->row['id'], 'network' => true ] ) ?>">Delete free IP addresses in a given network</a>
            </li>
        </ul>


    <?php endif;?>




</div>
