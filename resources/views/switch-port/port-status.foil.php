<?php if( $t->data[ 'params'][ 'portStates'][ $t->state ] == 'up' ): ?>

    <span class="label label-success">Up</span>

<?php elseif( $t->data[ 'params'][ 'portStates'][ $t->state ] == 'down' ): ?>

    <span class="label label-danger">Down</span>

<?php else: ?>

    <span class="label label-warning"><?= $t->data[ 'params'][ 'portStates'][ $t->state ] ?></span>

<?php endif; ?>
