<?php if( $t->data[ 'params'][ "contactGroups" ] && isset( $t->data[ 'params'][ "contactGroups" ][ $t->data[ 'params'][ "cg" ] ] )): ?>
    <h3> Filtered for Group : <?= $t->data[ 'params'][ "contactGroups" ][ $t->data[ 'params'][ "cg" ] ] ?> </h3>
<?php endif; ?>

