<?php foreach( $t->c->virtualInterfaces as $vi ): ?>
    <?php if( $vi->type() === $t->type ): ?>
        <?= $t->insert( 'customer/overview-tabs/ports/port', [ 'c' => $t->c ,'vi' => $vi, 'nbVi' => $t->nbVi, 'isSuperUser' => $t->isSuperUser ] ); ?>
        <?php $t->nbVi++ ?>
    <?php endif;?>
<?php endforeach; ?>