<div class="btn-group btn-group-sm">
    <a class="btn btn-outline-secondary" href="<?= route($t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>"  title="Preview">
        <i class="fa fa-eye"></i>
    </a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-outline-secondary" href="<?= route($t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
            <i class="fa fa-pencil"></i>
        </a>
        <a class="btn btn-outline-secondary d2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' data-object-id="<?= $t->row[ 'id' ] ?>" data-cust-id="<?= Auth::getUser()->isSuperUser() ? '0' : Auth::getUser()->getCustomer()->getId() ?>" href="#" title="Delete">
            <i class="fa fa-trash"></i>
        </a>
    <?php endif;?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route($t->feParams->route_prefix . '@welcome-email' , [ 'id' => $t->row[ 'id' ], 'resend' => 1 ] ) ?>">
                Resend welcome email
            </a>
            <a class="dropdown-item" href="<?= route( "login-history@view",     [ 'id' => $t->row['id'] ]   )    ?>">
                Login history
            </a>
            <a class="dropdown-item <?= $t->row[ 'disabled' ] || Auth::getUser()->getId() == $t->row['id'] ? "disabled" : "" ?>" href="<?= route( "switch-user@switch", [ "id" => $t->row['id'] ] ) ?>">
                Login as
            </a>
        </ul>
    <?php endif;?>
</div>
