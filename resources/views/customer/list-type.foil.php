<?php if( $t->cust->typeAssociate() ): ?>
    <span class="badge badge-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->typeProBono() ): ?>
    <span class="badge badge-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->typeInternal() ): ?>
    <span class="badge badge-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->typeFull() ): ?>
    <span class="badge badge-success">FULL MEMBER</span>
<?php else: ?>
    <span class="badge badge-dark">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>