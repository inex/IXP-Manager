<?php if( $t->cust->typeAssociate() ): ?>
    <span class="badge badge-warning">ASSOCIATE</span>
<?php elseif( $t->cust->typeProBono() ): ?>
    <span class="badge badge-info">PROBONO</span>
<?php elseif( $t->cust->typeInternal() ): ?>
    <span class="badge badge-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->typeFull() ): ?>
    <span class="badge badge-success">FULL</span>
<?php else: ?>
    <span class="badge badge-dark">UNKNOWN TYPE</span>
<?php endif; ?>
<?php if( $t->cust->isReseller ): ?>
    <span class="badge badge-info">RESELLER</span>
<?php elseif( $t->cust->reseller ): ?>
    <span class="badge badge-warning">RESOLD</span>
<?php endif; ?>
