<?php
/** @var Foil\Template\Template $t */
?>
<?php if( $t->cust->isTypeAssociate() ): ?>
    <span class="label lb-sm label-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->isTypeProBono() ): ?>
    <span class="label lb-sm label-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->isTypeInternal() ): ?>
    <span class="label lb-sm label-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->isTypeFull() == \Entities\Customer::TYPE_FULL ): ?>
    <span class="label lb-sm label-success">FULL MEMBER</span>
<?php else: ?>
    <span class="label">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>

<?php if( $t->cust->hasLeft() ): ?>
    <span class="label lb-sm label-danger">ACCOUNT CLOSED</span>
<?php endif; ?>
<?php if( $t->resellerMode() ): ?>
    <?php if( $t->cust->getIsReseller() ): ?>
        <span class="label lb-sm label-default">RESELLER</span>
    <?php elseif( $t->cust->getReseller() ): ?>
        <span class="label lb-sm label-default">RESOLD CUSTOMER</span>
    <?php endif; ?>
<?php endif; ?>

<?php if( !$t->cust->isTypeAssociate()  &&  !$t->cust->statusIsNormal() ): ?>

    <?php if( $t->cust->statusIsNotConnected() ): ?>
        <span class="label lb-sm label-warning">NOT CONNECTED</span>
    <?php elseif( $t->cust->statusIsSuspended() ): ?>
        <span class="label lb-sm label-important">SUSPENDED</span>
    <?php else: ?>
        <span class="label lb-sm label-primary">UNKNOWN CUSTOMER STATUS</span>
    <?php endif; ?>
<?php endif; ?>