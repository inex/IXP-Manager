<?php if( $t->cust->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
    <span class="label label-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_PROBONO ): ?>
    <span class="label label-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_INTERNAL ): ?>
    <span class="label label-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_FULL ): ?>
    <span class="label label-success">FULL MEMBER</span>
<?php else: ?>
    <span class="label">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>