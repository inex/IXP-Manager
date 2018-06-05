<?php if( isset( $t->data[ 'params'][ "infra" ] ) && $t->data[ 'params'][ "infra" ] ): ?>

    <div class="alert alert-info">

         Only showing switches for: <b><?= $t->data[ 'params'][ "infra" ]->getName() ?></b>.
        <a style="float: right" class="btn btn-xs btn-default" href="<?= route( "switch@list" , [ "infra" => 0 ] ) ?>" class='btn btn-small'>Show All</a>

    </div>

<?php endif;?>