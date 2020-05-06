<?php if( $t->cust->isTypeAssociate() ): ?>
    <span class="badge badge-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->isTypeProBono() ): ?>
    <span class="badge badge-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->isTypeRouteServer() ): ?>
    <span class="badge badge-primary">ROUTE SERVER</span>
<?php elseif( $t->cust->isTypeInternal() ): ?>
    <span class="badge badge-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->isTypeFull() ): ?>
    <span class="badge badge-success">FULL MEMBER</span>
<?php else: ?>
    <span class="badge badge-dark">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>
