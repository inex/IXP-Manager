<?php foreach( $t->c->getVirtualInterfaces() as $vi ): ?>
    <?php if( $vi->getType() == $t->type ): ?>
        <?= $t->insert( 'customer/overview-tabs/ports/port', [ 'c' => $t->c ,'vi' => $vi, 'nbVi' => $t->nbVi ] ); ?>
        <?php $t->nbVi++ ?>
    <?php endif;?>
<?php endforeach; ?>