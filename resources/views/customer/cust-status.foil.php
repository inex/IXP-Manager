<?php if( $t->cust->getDateLeave() != null && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '0000-00-00' && $t->cust->getDateLeave()->format( 'Y-m-d' ) != '-0001-11-30' ): ?>
    <span class="label label-danger">CLOSED</span>
<?php else: ?>
    <?php if( $t->cust->getStatus() == \Entities\Customer::STATUS_SUSPENDED ): ?>
        <span class="label label-important">SUSPENDED</span>
    <?php elseif( $t->cust->getStatus() == \Entities\Customer::STATUS_NORMAL || ( $t->cust->getStatus() == \Entities\Customer::TYPE_ASSOCIATE && $t->cust->getStatus() == \Entities\Customer::STATUS_NOTCONNECTED ) ): ?>
        <span class="label label-success">NORMAL</span>
    <?php elseif( $t->cust->getStatus() == \Entities\Customer::STATUS_NOTCONNECTED ): ?>
        <span class="label label-warning">NOT CONNECTED</span>
    <?php else: ?>
        <span class="label">{*$cconf.mapper[$row.$col]*}</span>
    <?php endif; ?>
<?php endif; ?>