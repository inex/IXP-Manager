<?php if( $t->cust->dateleave && \Carbon\Carbon::instance( $t->cust->dateleave )->format( 'Y-m-d' ) !== '0000-00-00' && \Carbon\Carbon::instance( $t->cust->dateleave )->format( 'Y-m-d' ) !== '-0001-11-30' ): ?>
    <span class="badge badge-danger">CLOSED</span>
<?php else: ?>
    <?php if( $t->cust->statusSuspended() ): ?>
        <span class="badge badge-warning">SUSPENDED</span>
    <?php elseif( $t->cust->statusNormal() || ( $t->cust->typeAssociate() && $t->cust->statusNotConnected() ) ): ?>
        <span class="badge badge-success">NORMAL</span>
    <?php elseif( $t->cust->statusNotConnected() ): ?>
        <span class="badge badge-warning">NOT CONNECTED</span>
    <?php else: ?>
        <span class="badge-dark">{*$cconf.mapper[$row.$col]*}</span>
    <?php endif; ?>
<?php endif; ?>