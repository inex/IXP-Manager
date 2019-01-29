
########################################################################################
########################################################################################
###
### Import filter
###
########################################################################################
########################################################################################

function fn_import ( int remote_as )
int set transit_asns;
{
    if !(avoid_martians()) then {
        bgp_large_community.add( IXP_LC_FILTERED_BOGON );
    }

    # Belt and braces: must have at least one ASN in the path
    if( bgp_path.len < 1 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_SHORT );
    }

    # Peer ASN == route's first ASN?
    if (bgp_path.first != remote_as ) then {
        bgp_large_community.add( IXP_LC_FILTERED_FIRST_AS_NOT_PEER_AS );
    }

    # Prevent BGP NEXT_HOP Hijacking
    if !( from = bgp_next_hop ) then {
        bgp_large_community.add( IXP_LC_FILTERED_NEXT_HOP_NOT_PEER_IP );
    }

    # Filter Known Transit Networks
    transit_asns = TRANSIT_ASNS;
    if (bgp_path ~ transit_asns) then {
        bgp_large_community.add( IXP_LC_FILTERED_TRANSIT_FREE_ASN );
    }

    # Belt and braces: no one needs an ASN path with > 64 hops, that's just broken
    if( bgp_path.len > 64 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_LONG );
    }

<?php if( $t->router->rpki() ): ?>

    # RPKI check
    if( roa_check( t_roa, net, bgp_path.last ) = ROA_INVALID ) then {
        bgp_large_community.add( IXP_LC_FILTERED_RPKI_INVALID );
    } else if( roa_check( t_roa, net, bgp_path.last ) = ROA_VALID ) then {
        bgp_large_community.add( IXP_LC_INFO_RPKI_VALID );
    } else {
        # RPKI unknown, keep checking and mark as unknown for info
        bgp_large_community.add( IXP_LC_INFO_RPKI_UNKNOWN );
    }

<?php else: ?>

    # Skipping RPKI check, protocol not enabled.
    bgp_large_community.add( IXP_LC_INFO_RPKI_NOT_CHECKED );

<?php endif; ?>


    # Route collector does not use IRRDB filtering
    bgp_large_community.add( IXP_LC_INFO_IRRDB_NOT_CHECKED );

    return true;
}


<?php foreach( $t->ints as $int ): ?>



########################################################################################
########################################################################################
###
### Collector clients
###
########################################################################################
########################################################################################


########################################################################################
########################################################################################
###
### AS<?= $int['autsys'] ?> - <?= $int['cname'] ?> - VLAN Interface #<?= $int['vliid'] ?>

protocol bgp pb_as<?= $int['autsys'] ?>_vli<?= $int['vliid'] ?>_ipv<?= $int['protocol'] ?? 4 ?> {
    description "AS<?= $int['autsys'] ?> - <?= $int['cname'] ?>";
    local as routerasn;
    source address routeraddress;
    strict bind yes;
    neighbor <?= $int['address'] ?> as <?= $int['autsys'] ?>;

    <?= $t->ipproto ?> {
        # As a route collector, we want to import everything and export nothing.
        # The import filter listed here just accepts everything but adds tags.
        import where fn_import( <?= $int['autsys'] ?> );
        export none;
        <?php if( $t->router->protocol() == 6 ): ?>missing lladdr ignore;<?php endif; ?>

    };

    <?php if( $int['bgpmd5secret'] && !$t->router->skipMD5() ): ?>password "<?= $int['bgpmd5secret'] ?>";<?php endif; ?>

}

<?php endforeach; ?>
