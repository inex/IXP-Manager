

########################################################################################
########################################################################################
#
# RPKI protocol configuration
#
########################################################################################
########################################################################################

<?php if( $t->router->rpki() ): ?>

roa<?= $t->router->protocol() ?> table t_roa;

protocol rpki {

    roa<?= $t->router->protocol() ?> { table t_roa; };

    remote "<?= config( 'ixp.rpki.host' ) ?>" port <?= config( 'ixp.rpki.port' ) ?>;

    retry keep 90;
    refresh keep 900;
    expire keep 172800;
}

<?php else:  /* $t->router->getRPKI() */ ?>

# RPKI not enabled for this router

<?php endif; ?>

