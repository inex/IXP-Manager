

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

    retry keep 90;
    refresh keep 900;
    expire keep 172800;
}

<?php if( config( 'ixp.rpki.rtr2.host' ) ): ?>

protocol rpki rpki2 {

    roa<?= $t->router->protocol ?> { table t_roa; };

    remote "<?= config( 'ixp.rpki.rtr2.host' ) ?>" port <?= config( 'ixp.rpki.rtr2.port' ) ?>;

    retry keep 90;
    refresh keep 900;
    expire keep 172800;
}

<?php endif; /* rtr2 */ ?>

/*
 * RPKI check for the path
 *
 * return: true means the filter should stop processing, false means keep processing
 */
function filter_rpki()
{
    # RPKI check
    if( roa_check( t_roa, net, bgp_path.last_nonaggregated ) = ROA_INVALID ) then {
        print "Tagging invalid ROA ", net, " for ASN ", bgp_path.last;
        bgp_large_community.add( IXP_LC_FILTERED_RPKI_INVALID );
        return true;
    }

    if( roa_check( t_roa, net, bgp_path.last_nonaggregated ) = ROA_VALID ) then {
        bgp_large_community.add( IXP_LC_INFO_RPKI_VALID );
        return true;
    }

    # RPKI unknown, keep checking and mark as unknown for info
    bgp_large_community.add( IXP_LC_INFO_RPKI_UNKNOWN );

    return false;
}

<?php else:  /* $t->router->getRPKI() */ ?>

    <?php if( $t->router->rpki ): ?>

# RPKI is enabled but not RPKI routers configured in your .env file.

    <?php else: ?>

# RPKI not enabled for this router

    <?php endif; ?>

<?php endif; ?>

