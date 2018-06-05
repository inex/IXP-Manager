<?php if( $t->adminState == 1 ): ?>

    <span class="label label-success">Up</span>

<?php elseif( $t->adminState == 2 ): ?>

    <span class="label label-danger">Down</span>

<?php elseif( $t->adminState == 3 ): ?>

    <span class="label label-warning">Testing</span>

<?php endif;?>


    &nbsp;/&nbsp;


<?php if( $t->operState == 1 ): ?>

    <span class="label label-success">Up</span>

<?php elseif( $t->operState == 2 ): ?>

    <span class="label label-danger">Down</span>

<?php elseif( $t->operState == 3 ): ?>

    <span class="label label-warning">Testing</span>

<?php elseif( $t->operState == 4 ): ?>

    <span class="label label-warning">Unknown</span>

<?php elseif( $t->operState == 5 ): ?>

    <span class="label label-warning">Dormant</span>

<?php elseif( $t->operState == 6 ): ?>

    <span class="label label-info">Not Present</span>

<?php elseif( $t->operState == 7 ): ?>

    <span class="label label-warning">Lower Layer Down</span>

<?php endif;?>
