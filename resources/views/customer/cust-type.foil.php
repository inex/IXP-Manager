<?php if( $t->cust->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
    <span class="label label-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_PROBONO ): ?>
    <span class="label label-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_INTERNAL ): ?>
    <span class="label label-inverse">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_FULL ): ?>
    <span class="label label-success">FULL MEMBER</span>
<?php else: ?>
    <span class="label">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>

<?php if( $t->cust->hasLeft() ): ?>
    <span class="label label-important">ACCOUNT CLOSED</span>
<?php endif; ?>
<?php if( $t->resellerMode ): ?>
    <?php if( $t->cust->getIsReseller() ): ?>
        <span class="label">RESELLER</span>
    <?php elseif( $t->cust->getReseller() ): ?>
        <span class="label">RESOLD CUSTOMER</span>
    <?php endif; ?>
<?php endif; ?>

<?php if( !$t->cust->isTypeAssociate()  &&  !$t->cust->statusIsNormal() ): ?>

    <?php if( $t->cust->statusIsNotConnected() ): ?>
        <span class="label label-warning">NOT CONNECTED</span>
    <?php elseif( $t->cust->statusIsSuspended() ): ?>
        <span class="label label-important">SUSPENDED</span>
    <?php else: ?>
        <span class="label label-inverse">UNKNOWN CUSTOMER STATUS</span>
    <?php endif; ?>
<?php endif; ?>