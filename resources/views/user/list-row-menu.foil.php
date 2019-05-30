<div class="btn-group btn-group-sm">
    <a class="btn btn-white" href="<?= route($t->feParams->route_prefix . '@view' , [ 'id' => $t->row[ 'id' ] ] ) ?>"  title="Preview">
        <i class="fa fa-eye"></i>
    </a>

    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
        <a class="btn btn-white" id='d2f-list-edit-<?= $t->row[ 'id' ] ?>' href="<?= route($t->feParams->route_prefix . '@edit' , [ 'id' => $t->row[ 'id' ] ] ) ?> " title="Edit">
            <i class="fa fa-pencil"></i>
        </a>

        <?php if( Auth::getUser()->isSuperUser() ): ?>
            <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $t->row[ 'id' ] ?>' data-nb-c2u="<?= $t->row[ 'nbC2U' ] ?>" href="<?= $t->data[ 'params'][ 'nbC2u'][ $t->row[ 'id' ] ] > 1 ? route( 'customer-to-user@delete' ) : route( $t->feParams->route_prefix . '@delete' )  ?>" title="Delete">
                <i class="fa fa-trash"></i>
            </a>
        <?php else: ?>
            <a class="btn btn-white d2f-list-delete" id='d2f-list-delete-<?= $t->data[ 'params'][ 'nbC2u'][ $t->row[ 'id' ] ] > 1 ? $t->row[ 'c2uid' ] : $t->row[ 'id' ] ?>' data-nb-c2u="<?= $t->row[ 'nbC2U' ] ?>" href="<?= $t->data[ 'params'][ 'nbC2u'][ $t->row[ 'id' ] ] > 1 ? route( 'customer-to-user@delete' ) : route( $t->feParams->route_prefix . '@delete' )  ?>" title="Delete">
                <i class="fa fa-trash"></i>
            </a>
        <?php endif; ?>


    <?php endif;?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <button id="d2f-more-options-<?= $t->row[ 'id' ] ?>" type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <form id="welcome-email" method="POST" action="<?= route($t->feParams->route_prefix.'@welcome-email' ) ?>">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="id" value="<?= $t->row[ 'id' ] ?>">
                <input type="hidden" name="resend" value="1">
                <button class="dropdown-item" type="submit">
                    Resend welcome email
                </button>
            </form>

            <a class="dropdown-item" href="<?= route( "login-history@view",     [ 'id' => $t->row['id'] ]   )    ?>">
                Login history
            </a>
            <a id="d2f-option-login-as-<?= $t->row[ 'id' ] ?>" class="dropdown-item <?= $t->row[ 'disabled' ] || Auth::getUser()->getId() == $t->row['id'] ? "disabled" : "" ?>" href="<?= route( "switch-user@switch", [ "id" => $t->row['id'] ] ) ?>">
                Login as
            </a>
        </ul>
    <?php endif;?>
</div>
