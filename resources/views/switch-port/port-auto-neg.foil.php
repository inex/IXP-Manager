<?php if( !$t->mauAutoNegAdminState ): ?>
    <span class="badge badge-info">
        Not Supported / Disabled
    </span>
<?php elseif( $t->mauAutoNegAdminState ): ?>
    <span class="badge badge-success">
        Enabled
    </span>
<?php else: ?>
    <span class="badge badge-danger">
        Disabled
    </span>
<?php endif; ?>