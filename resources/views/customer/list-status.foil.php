<?php if( $t->cust->getDateLeave() != null && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '0000-00-00' && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '-0001-11-30' ): ?>
    <span class="label lb-xs label-danger">CLOSED</span>
<?php else: ?>
    <?php if( $t->cust->statusIsSuspended() ): ?>
        <span class="label lb-xs label-important">SUSPENDED</span>
    <?php elseif( $t->cust->statusIsNormal() || ( $t->cust->isTypeAssociate() && $t->cust->statusIsNotConnected() ) ): ?>
        <span class="label lb-xs label-success">NORMAL</span>
    <?php elseif( $t->cust->statusIsNotConnected() ): ?>
        <span class="label lb-xs label-warning">NOT CONNECTED</span>
    <?php else: ?>
        <span class="label lb-xs">{*$cconf.mapper[$row.$col]*}</span>
    <?php endif; ?>
<?php endif; ?>