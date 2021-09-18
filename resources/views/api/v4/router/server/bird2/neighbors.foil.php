<?php
/*
 * Bird Route Server Configuration Template
 *
 *
 * You should not need to edit these files - instead use your own custom skins. If
 * you can't effect the changes you need with skinning, consider posting to the mailing
 * list to see if it can be achieved / incorporated.
 *
 * Skinning: https://ixp-manager.readthedocs.io/en/latest/features/skinning.html
 *
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

<?php
    // NOTE: fvliid is used below to distinguish between multiple VLAN interfaces
    //   for the same customer in the same peering LAN

    // only define one filter per ASN
use IXP\Models\Aggregators\IrrdbAggregator;

$asn_filters = [];
?>


########################################################################################
########################################################################################
#
# Route server clients
#
########################################################################################
########################################################################################

<?php foreach( $t->ints as $int ):

        // do not set up a session to ourselves!
        if( $int['autsys'] == $t->router->asn ):
            continue;
        endif;
?>

########################################################################################
########################################################################################
###
### AS<?= $int['autsys'] ?> - <?= $int['cname'] ?> - VLAN Interface #<?= $int['vliid'] ?>


<?= $t->ipproto ?> table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;

<?php
    if( !in_array( $int['autsys'], $asn_filters ) ):

        $asn_filters[] = $int['autsys'];
?>


filter f_import_as<?= $int['autsys'] ?>

prefix set allnet;
ip set allips;
int set allas;
{

<?php
    // We allow per customer AS headers here which IXPs can define as skinned files.
    // For example, to solve a Facebook issue, INEX created the following:
    //     resources/skins/inex/api/v4/router/server/bird2/f_import_as32934.foil.php
    echo $t->insertif( 'api/v4/router/server/bird2/f_import_as' . $int['autsys'] );

?>

    # Filter small prefixes
<?php if( $t->router->protocol == 6 ): ?>
    if ( net ~ [ ::/0{<?= config( 'ixp.irrdb.min_v6_subnet_size', 48 ) == 128 ? 128 : config( 'ixp.irrdb.min_v6_subnet_size', 48 ) + 1 ?>,128} ] ) then {
<?php else: ?>
    if ( net ~ [ 0.0.0.0/0{<?= config( 'ixp.irrdb.min_v4_subnet_size', 24 ) == 32 ? 32 : config( 'ixp.irrdb.min_v4_subnet_size', 24 ) + 1 ?>,32} ] ) then {
<?php endif; ?>
        bgp_large_community.add( IXP_LC_FILTERED_PREFIX_LEN_TOO_LONG );
        accept;
    }


    if !(avoid_martians()) then {
        bgp_large_community.add( IXP_LC_FILTERED_BOGON );
        accept;
    }

    # Belt and braces: must have at least one ASN in the path
    if( bgp_path.len < 1 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_SHORT );
        accept;
    }

    # Peer ASN == route's first ASN?
    if (bgp_path.first != <?= $int['autsys'] ?> ) then {
        bgp_large_community.add( IXP_LC_FILTERED_FIRST_AS_NOT_PEER_AS );
        accept;
    }

    # set of all IPs this ASN uses to peer with on this VLAN
    allips = [ <?= implode( ', ', $int['allpeeringips'] ) ?> ];

    # Prevent BGP NEXT_HOP Hijacking
    if !( from = bgp_next_hop ) then {

        # need to differentiate between same ASN next hop or actual next hop hijacking
        if( bgp_next_hop ~ allips ) then {
            bgp_large_community.add( IXP_LC_INFO_SAME_AS_NEXT_HOP );
        } else {
            # looks like hijacking (intentional or not)
            bgp_large_community.add( IXP_LC_FILTERED_NEXT_HOP_NOT_PEER_IP );
            accept;
        }
    }


    # Filter Known Transit Networks
    if filter_has_transit_path() then accept;

    # Belt and braces: no one needs an ASN path with > 64 hops, that's just broken
    if( bgp_path.len > 64 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_LONG );
        accept;
    }


        <?php
    // Only do IRRDB ASN filtering if this is enabled per client:
    $asns = [];
    if( $int['irrdbfilter'] ?? true ):
        $asns = IrrdbAggregator::asnsForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );
        if( count( $asns ) ):
?>

    allas = [ <?php echo $t->softwrap( $asns, 10, ", ", ",", 14, 7 ); ?>

    ];

<?php   else: ?>

    allas = [ <?= $int['autsys'] ?> ];

<?php   endif; ?>

    # Ensure origin ASN is in the neighbors AS-SET
    if !(bgp_path.last_nonaggregated ~ allas) then {
        bgp_large_community.add( IXP_LC_FILTERED_IRRDB_ORIGIN_AS_FILTERED );
        accept;
    }

<?php
    endif; ?>

<?php if( $t->router->rpki && config( 'ixp.rpki.rtr1.host' ) ): ?>

    # RPKI test - if it's INVALID or VALID, we are done
    if filter_rpki() then accept;

<?php else: ?>

    # Skipping RPKI check -> RPKI not enabled / configured correctly.
    bgp_large_community.add( IXP_LC_INFO_RPKI_NOT_CHECKED );

<?php endif; ?>


<?php
    // Only do IRRDB prefix filtering if this is enabled per client:
    $prefixes = [];
    if( $int['irrdbfilter'] ?? true ):

        $prefixes = IrrdbAggregator::prefixesForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );

        if( count( $prefixes ) ):
?>

    allnet = [ <?php echo $t->softwrap( $int['rsmorespecifics']
            ? $t->bird()->prefixExactToLessSpecific( $prefixes, $t->router->protocol, config( 'ixp.irrdb.min_v' . $t->router->protocol . '_subnet_size' ) )
            : $prefixes, 4, ", ", ",", 15, $t->router->protocol === 6 ? 36 : 26 ); ?>

    ];

    <?php unset( $prefixes ); ?>

    if ! (net ~ allnet) then {
        bgp_large_community.add( IXP_LC_FILTERED_IRRDB_PREFIX_FILTERED );
        bgp_large_community.add( <?= $int['rsmorespecifics'] ? 'IXP_LC_INFO_IRRDB_FILTERED_LOOSE' : 'IXP_LC_INFO_IRRDB_FILTERED_STRICT' ?> );
        accept;
    } else {
        bgp_large_community.add( IXP_LC_INFO_IRRDB_VALID );
    }

<?php   else: ?>

    # Deny everything because the IRR database returned nothing
    bgp_large_community.add( IXP_LC_FILTERED_IRRDB_PREFIX_FILTERED );
    bgp_large_community.add( IXP_LC_INFO_IRRDB_PREFIX_EMPTY );
    accept;

<?php   endif; ?>

<?php else: ?>

    # This ASN was configured not to use IRRDB filtering
    bgp_large_community.add( IXP_LC_INFO_IRRDB_NOT_CHECKED );

<?php endif; ?>

    accept;
}


# The route server export filter exists as the export gateway on the BGP protocol.
#
# Remember that standard IXP community filtering has already happened on the
# master -> bgp protocol pipe.

filter f_export_as<?= $int['autsys'] ?>
{

<?php
    // We allow per customer AS export code here which IXPs can define as skinned files.
    // For example, to solve a Facebook issue, INEX created the following:
    //     resources/skins/api/v4/router/server/bird2/f_export_as32934.foil.php
    echo $t->insertif( 'api/v4/router/server/bird2/f_export_as' . $int['autsys'] );
?>


    # we should strip our own communities which we used for the looking glass
    bgp_large_community.delete( [( routeserverasn, *, * )] );
    bgp_community.delete( [( routeserverasn, * )] );

    # default position is to accept:
    accept;

}






    <?php
    endif; // if( !in_array( $asn_filters[ $int['autsys'] ] ) ):
?>

protocol pipe pp_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?> {
        description "Pipe for AS<?= $int['autsys'] ?> - <?= $int['cname'] ?> - VLAN Interface <?= $int['vliid'] ?>";
        table master<?= $t->router->protocol ?>;
        peer table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;
        import filter f_export_to_master;
        export where ixp_community_filter(<?= $int['autsys'] ?>);
}

protocol bgp pb_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?> from tb_rsclient {
        description "AS<?= $int['autsys'] ?> - <?= $int['cname'] ?>";
        neighbor <?= $int['address'] ?> as <?= $int['autsys'] ?>;
        <?= $t->ipproto ?> {
            import limit <?= $int['maxprefixes'] ?> action restart;
            import filter f_import_as<?= $int['autsys'] ?>;
            table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;
            export filter f_export_as<?= $int['autsys'] ?>;
        };
        <?php if( $int['bgpmd5secret'] && !$t->router->skip_md5 ): ?>password "<?= $int['bgpmd5secret'] ?>";<?php endif; ?>

}

<?php endforeach; ?>

