<?php if( !$t->mauAutoNegAdminState ): ?>

    <span class="label label-info">Not Supported / Disabled</span>

<?php elseif( $t->mauAutoNegAdminState ): ?>

    <span class="label label-success">Enabled</span>

<?php else: ?>

    <span class="label label-danger">Disabled</span>

<?php endif; ?>