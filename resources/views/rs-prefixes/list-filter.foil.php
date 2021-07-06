<div class="btn-group btn-group-sm" role="group">
    <button type="button" class="btn btn-white dropdown-toggle center-dd-caret d-flex " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php if( $t->protocol ): ?> Filtered for IPv<?= $t->protocol ?><?php else: ?>Limit to Protocol...<?php endif;?>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item <?=( $t->protocol )?: "active" ?>" id="protocol-0" href="<?= route( "rs-prefixes@view", [ 'cust' => $t->c->id ] ) ?>">
            All Protocols
        </a>
        <a class="dropdown-item <?=( $t->protocol !== '4' )?: "active" ?>" id="protocol-4" href="<?= route( "rs-prefixes@view", [ 'cust' => $t->c->id, 'protocol' => 4 ] ) ?>">
            IPv4 Only
        </a>
        <a class="dropdown-item <?=( $t->protocol !== '6' )?: "active" ?>" id="protocol-6" href="<?= route( "rs-prefixes@view", [ 'cust' => $t->c->id, 'protocol' => 6  ] ) ?>">
            IPv6 Only
        </a>
    </div>
</div>