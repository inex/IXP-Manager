<div class="btn-group">

    <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

    <?php if( !isset( $t->data[ 'feParams' ]->readonly ) || !$t->data[ 'feParams' ]->readonly ): ?>
        <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
        <a class="btn btn-sm btn-default" id='list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-related="<?= $t->row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
    <?php endif;?>

    <a class="btn btn-sm btn-default dropdown-toggle" href="#" data-toggle="dropdown">
        <span class="caret"></span>
    </a>
    
    <ul class="dropdown-menu dropdown-menu-right">
        <li>
            <a href="<?= url( '/switch/list/infra/' . $t->row['id'] ) ?>">View Switches</a>
            <a href="<?= url( '/vlan/list/infra/' . $t->row['id'] ) ?>">View All VLANs</a>
            <a href="<?= url( '/vlan/list/infra/' . $t->row['id'] . '/publiconly/1' ) ?>">View Public VLANs</a>
            <a href="<?= url( '/vlan/private/infra/' . $t->row['id'] ) ?>">View Private VLANs</a>
        </li>
    </ul>
</div>
