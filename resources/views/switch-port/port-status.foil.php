<?php if( $t->data[ 'params'][ 'portStates'][ $t->state ] === 'up' ): ?>
    <span class="badge badge-success">
        Up
    </span>
<?php elseif( $t->data[ 'params'][ 'portStates'][ $t->state ] === 'down' ): ?>
    <span class="badge badge-danger">
        Down
    </span>
<?php else: ?>
    <span class="badge badge-warning">
        <?= $t->data[ 'params'][ 'portStates'][ $t->state ] ?>
    </span>
<?php endif; ?>