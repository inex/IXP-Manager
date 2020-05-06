<?php
/** @var Foil\Template\Template $t */
?>
<?php if( $t->cust->isTypeAssociate() ): ?>
    <span class="badge badge-warning tw-p-1">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->isTypeProBono() ): ?>
    <span class="badge badge-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->isTypeRouteServer() ): ?>
    <span class="badge badge-primary">ROUTE SERVER</span>
<?php elseif( $t->cust->isTypeInternal() ): ?>
    <span class="badge  badge-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->isTypeFull() == \Entities\Customer::TYPE_FULL ): ?>
    <span class="badge  badge-success">FULL MEMBER</span>
<?php else: ?>
    <span class="badge">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>

<?php if( $t->cust->hasLeft() ): ?>
    <span class="badge  badge-danger">ACCOUNT CLOSED</span>
<?php endif; ?>
<?php if( $t->resellerMode() ): ?>
    <?php if( $t->cust->getIsReseller() ): ?>
        <span class="badge  badge-secondary">RESELLER</span>
    <?php elseif( $t->cust->getReseller() ): ?>
        <span class="badge  badge-secondary">RESOLD CUSTOMER</span>
    <?php endif; ?>
<?php endif; ?>

<?php if( !$t->cust->isTypeAssociate()  &&  !$t->cust->statusIsNormal() ): ?>

    <?php if( $t->cust->statusIsNotConnected() ): ?>
        <span class="badge  badge-warning">NOT CONNECTED</span>
    <?php elseif( $t->cust->statusIsSuspended() ): ?>
        <span class="badge  badge-danger">SUSPENDED</span>
    <?php else: ?>
        <span class="badge  badge-primary">UNKNOWN CUSTOMER STATUS</span>
    <?php endif; ?>
<?php endif; ?>
