<div class="btn-group">

    <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>"  title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
        <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' href="#" data-object-id="<?= $t->row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
    <?php endif;?>

    <a class="btn btn-sm btn-default dropdown-toggle" href="#" data-toggle="dropdown">
        <span class="caret"></span>
    </a>

    <ul class="dropdown-menu dropdown-menu-right">
        <li>
            <a href="<?= route($t->feParams->route_prefix . '@welcome-email' , [ 'id' => $t->row[ 'id' ], 'resend' => 1 ] ) ?>">Resend welcome email</a>
            <a href="<?= route( "login-history@view",     [ 'id' => $t->row['id'] ]   )    ?>">Login history</a>
            <a href="<?= route( "switch-user@switch", [ "id" => $t->row['id'] ] ) ?>">Login as</a>
        </li>
    </ul>

</div>
