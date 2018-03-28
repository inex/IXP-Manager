<?php if( $t->cust->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
    <span class="label lb-xs label-warning">ASSOCIATE MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_PROBONO ): ?>
    <span class="label lb-xs label-info">PROBONO MEMBER</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_INTERNAL ): ?>
    <span class="label lb-xs label-primary">INTERNAL INFRASTRUCTURE</span>
<?php elseif( $t->cust->getType() == \Entities\Customer::TYPE_FULL ): ?>
    <span class="label lb-xs label-success">FULL MEMBER</span>
<?php else: ?>
    <span class="label lb-xs">UNKNOWN MEMBER TYPE</span>
<?php endif; ?>