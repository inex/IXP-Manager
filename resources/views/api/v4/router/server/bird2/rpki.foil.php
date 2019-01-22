

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

    remote "127.0.0.1" port 3323;

    retry keep 5;
    refresh keep 30;
    expire 600;
}


/*
 * RPKI check for the path
 *
 * return: true means the filter should stop processing, false means keep processing
 */
function filter_rpki()
{
    # RPKI check
    if( roa_check( t_roa, net, bgp_path.last ) = ROA_INVALID ) then {
        print "Tagging invalid ROA ", net, " for ASN ", bgp_path.last;
        bgp_large_community.add( IXP_LC_FILTERED_RPKI_INVALID );
        return true;
    }

    if( roa_check( t_roa, net, bgp_path.last ) = ROA_VALID ) then {
        bgp_large_community.add( IXP_LC_INFO_RPKI_VALID );
        return true;
    }

    # RPKI unknown, keep checking and mark as unknown for info
    bgp_large_community.add( IXP_LC_INFO_RPKI_UNKNOWN );

    return false;
}

<?php else:  /* $t->router->getRPKI() */ ?>

# RPKI not enabled for this router

<?php endif; ?>

