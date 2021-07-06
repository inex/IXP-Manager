<?php if( $t->adminState === 1 ): ?>
    <span class="badge badge-success">
        Up
    </span>
<?php elseif( $t->adminState === 2 ): ?>
    <span class="badge badge-danger">
        Down
    </span>
<?php elseif( $t->adminState === 3 ): ?>
    <span class="badge badge-warning">
        Testing
    </span>
<?php endif;?>
    &nbsp;/&nbsp;
<?php if( $t->operState === 1 ): ?>
    <span class="badge badge-success">
        Up
    </span>
<?php elseif( $t->operState === 2 ): ?>
    <span class="badge badge-danger">
        Down
      </span>
<?php elseif( $t->operState === 3 ): ?>
    <span class="badge badge-warning">
        Testing
    </span>
<?php elseif( $t->operState === 4 ): ?>
    <span class="badge badge-warning">
        Unknown
    </span>
<?php elseif( $t->operState === 5 ): ?>
    <span class="badge badge-warning">
        Dormant
    </span>
<?php elseif( $t->operState === 6 ): ?>
    <span class="badge badge-info">
        Not Present
    </span>
<?php elseif( $t->operState === 7 ): ?>
    <span class="badge badge-warning">
        Lower Layer Down
    </span>
<?php endif;?>