<?php if( $t->cust->getDateLeave() != null && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '0000-00-00' && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '-0001-11-30' ): ?>
    <span class="badge badge-danger">CLOSED</span>
<?php else: ?>
    <?php if( $t->cust->statusIsSuspended() ): ?>
        <span class="badge badge-important">SUSPENDED</span>
    <?php elseif( $t->cust->statusIsNormal() || ( $t->cust->isTypeAssociate() && $t->cust->statusIsNotConnected() ) ): ?>
        <span class="badge badge-success">NORMAL</span>
    <?php elseif( $t->cust->statusIsNotConnected() ): ?>
        <span class="badge badge-warning">NOT CONNECTED</span>
    <?php else: ?>
        <span class="badge-dark">{*$cconf.mapper[$row.$col]*}</span>
    <?php endif; ?>
<?php endif; ?>