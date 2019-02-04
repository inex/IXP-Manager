<?php if( isset( $t->data[ 'params'][ "infra" ] ) && $t->data[ 'params'][ "infra" ] ): ?>

    <div class="alert alert-info mt-4" role="alert">
        <div class="d-flex align-items-center col-sm-12">
            <div class="text-center">
                <i class="fa fa-question-circle fa-2x"></i>
            </div>
            <div class="d-flex col-sm-12">
                <div class="mr-auto">
                    Only showing switches for: <b><?= $t->data[ 'params'][ "infra" ]->getName() ?></b>.
                </div>
                <div class="float-right">
                    <a style="" class="btn btn-sm btn-outline-secondary" href="<?= route( "switch@list" , [ "infra" => 0 ] ) ?>" class='btn btn-small'>
                        Show All
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php endif;?>