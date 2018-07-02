<?php if( $t->cust->isTypeAssociate() ): ?>
    <span class="label lb-xs label-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->isTypeProBono() ): ?>
    <span class="label lb-xs label-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->isTypeInternal() ): ?>
    <span class="label lb-xs label-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->isTypeFull() ): ?>
    <span class="label lb-xs label-success">FULL MEMBER</span>
<?php else: ?>
    <span class="label lb-xs">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>