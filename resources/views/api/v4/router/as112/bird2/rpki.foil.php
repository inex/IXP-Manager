

########################################################################################
########################################################################################
#
# RPKI protocol configuration
#
########################################################################################
########################################################################################

<?php if( $t->router->rpki && config( 'ixp.rpki.rtr1.host' ) ): ?>

roa<?= $t->router->protocol ?> table t_roa;

protocol rpki rpki1 {

    roa<?= $t->router->protocol ?> { table t_roa; };

    remote "<?= config( 'ixp.rpki.rtr1.host' ) ?>" port <?= config( 'ixp.rpki.rtr1.port' ) ?>;
<?php if( config( 'ixp.rpki.rtr1.min_version' ) !== null ) { echo "    min version " . (int)config( 'ixp.rpki.rtr1.min_version' ) . ";\n"; } ?>
<?php if( config( 'ixp.rpki.rtr1.max_version' ) !== null ) { echo "    max version " . (int)config( 'ixp.rpki.rtr1.max_version' ) . ";\n"; } ?>

}

    <?php if( config( 'ixp.rpki.rtr2.host' ) ): ?>

protocol rpki rpki2 {

    roa<?= $t->router->protocol ?> { table t_roa; };

    remote "<?= config( 'ixp.rpki.rtr2.host' ) ?>" port <?= config( 'ixp.rpki.rtr2.port' ) ?>;
<?php if( config( 'ixp.rpki.rtr2.min_version' ) !== null ) { echo "    min version " . (int)config( 'ixp.rpki.rtr2.min_version' ) . ";\n"; } ?>
<?php if( config( 'ixp.rpki.rtr2.max_version' ) !== null ) { echo "    max version " . (int)config( 'ixp.rpki.rtr2.max_version' ) . ";\n"; } ?>

}

    <?php endif; /* rtr2 */ ?>

<?php else:  /* $t->router->getRPKI() */ ?>

    <?php if( $t->router->rpki ): ?>

# RPKI is enabled but not RPKI routers configured in your .env file.

    <?php else: ?>

# RPKI not enabled for this router

    <?php endif; ?>

<?php endif; ?>
