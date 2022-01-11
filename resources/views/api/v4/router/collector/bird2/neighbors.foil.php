<?php
/*
 * Bird Route Collector Configuration Template
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
    $asn_filters = [];
?>

########################################################################################
########################################################################################
#
# Route collector clients
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


<?php
    if( !in_array( $int['autsys'], $asn_filters ) ):

        $asn_filters[] = $int['autsys'];
?>


function f_import_as<?= $int['autsys'] ?>()

prefix set allnet;
ip set allips;
int set allas;
{

    <?= $t->insert( 'api/v4/router/collector/bird2/import-pre-extra', [ 'int' => $int ] ) ?>

    # Filter small prefixes
<?php if( $t->router->protocol == 6 ): ?>
    if ( net ~ [ ::/0{<?= config( 'ixp.irrdb.min_v6_subnet_size', 48 ) == 128 ? 128 : config( 'ixp.irrdb.min_v6_subnet_size', 48 ) + 1 ?>,128} ] ) then {
<?php else: ?>
    if ( net ~ [ 0.0.0.0/0{<?= config( 'ixp.irrdb.min_v4_subnet_size', 24 ) == 32 ? 32 : config( 'ixp.irrdb.min_v4_subnet_size', 24 ) + 1 ?>,32} ] ) then {
<?php endif; ?>
        bgp_large_community.add( IXP_LC_FILTERED_PREFIX_LEN_TOO_LONG );
    }


    if !(avoid_martians()) then {
        bgp_large_community.add( IXP_LC_FILTERED_BOGON );
    }

    # Belt and braces: must have at least one ASN in the path
    if( bgp_path.len < 1 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_SHORT );
        # we won't continue in this case: zero path asn is broken
        return true;
    }

    # Peer ASN == route's first ASN?
    if (bgp_path.first != <?= $int['autsys'] ?> ) then {
        bgp_large_community.add( IXP_LC_FILTERED_FIRST_AS_NOT_PEER_AS );
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
        }
    }

    # Filter Known Transit Networks
    filter_has_transit_path();

    # Belt and braces: no one needs an ASN path with > 64 hops, that's just broken
    if( bgp_path.len > 64 ) then {
        bgp_large_community.add( IXP_LC_FILTERED_AS_PATH_TOO_LONG );
    }

<?php
    // Only do IRRDB ASN filtering if this is enabled per client:
    $asns = [];
    if( ( $int['rsclient'] ?? false ) && $int['irrdbfilter'] ?? true ):

        $asns = \IXP\Models\Aggregators\IrrdbAggregator::asnsForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );
        if( count( $asns ) ): ?>

    allas = [ <?php echo $t->softwrap( $asns, 10, ", ", ",", 14, 7 ); ?>

    ];

        <?php   else: ?>

    allas = [ <?= $int['autsys'] ?> ];

        <?php   endif; ?>

    # Ensure origin ASN is in the neighbors AS-SET
    if !(bgp_path.last_nonaggregated ~ allas) then {
        bgp_large_community.add( IXP_LC_FILTERED_IRRDB_ORIGIN_AS_FILTERED );
    }

<?php endif; ?>



<?php if( $t->router->rpki && config( 'ixp.rpki.rtr1.host' ) ): ?>

    # RPKI check
    if( roa_check( t_roa, net, bgp_path.last_nonaggregated ) = ROA_INVALID ) then {
        bgp_large_community.add( IXP_LC_FILTERED_RPKI_INVALID );
    } else if( roa_check( t_roa, net, bgp_path.last_nonaggregated ) = ROA_VALID ) then {
        bgp_large_community.add( IXP_LC_INFO_RPKI_VALID );
    } else {
        # RPKI unknown, keep checking and mark as unknown for info
        bgp_large_community.add( IXP_LC_INFO_RPKI_UNKNOWN );
    }

<?php else: ?>

    # Skipping RPKI check -> RPKI not enabled / configured correctly.
    bgp_large_community.add( IXP_LC_INFO_RPKI_NOT_CHECKED );

<?php endif; ?>




<?php
    // Only do IRRDB prefix filtering if this is enabled per client:
    $prefixes = [];
    if( ( $int['rsclient'] ?? false ) && $int['irrdbfilter'] ?? true ):

        $prefixes = \IXP\Models\Aggregators\IrrdbAggregator::prefixesForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );

        if( count( $prefixes ) ): ?>

    allnet = [ <?php echo $t->softwrap( $int['rsmorespecifics']
        ? $t->bird()->prefixExactToLessSpecific( $prefixes, $t->router->protocol, config( 'ixp.irrdb.min_v' . $t->router->protocol . '_subnet_size' ) )
        : $prefixes, 4, ", ", ",", 15, $t->router->protocol == 6 ? 36 : 26 ); ?>

    ];

    if ! (net ~ allnet) then {
        if bgp_large_community ~ [IXP_LC_INFO_RPKI_VALID] then {
            bgp_large_community.add( IXP_LC_INFO_IRRDB_INVALID );
        } else {
            bgp_large_community.add( IXP_LC_FILTERED_IRRDB_PREFIX_FILTERED );
        }

        bgp_large_community.add( <?= $int['rsmorespecifics'] ? 'IXP_LC_INFO_IRRDB_FILTERED_LOOSE' : 'IXP_LC_INFO_IRRDB_FILTERED_STRICT' ?> );
    } else {
        bgp_large_community.add( IXP_LC_INFO_IRRDB_VALID );
    }

        <?php   else: ?>

    # Deny everything because the IRR database returned nothing
    bgp_large_community.add( IXP_LC_FILTERED_IRRDB_PREFIX_FILTERED );
    bgp_large_community.add( IXP_LC_INFO_IRRDB_PREFIX_EMPTY );

        <?php   endif; ?>

    <?php else: ?>

    # This ASN was configured not to use IRRDB filtering
    bgp_large_community.add( IXP_LC_INFO_IRRDB_NOT_CHECKED );

<?php endif; ?>



    return true;
}


<?php endif; ?>


protocol bgp pb_as<?= $int['autsys'] ?>_vli<?= $int['vliid'] ?>_ipv<?= $int['protocol'] ?? 4 ?> {
    description "AS<?= $int['autsys'] ?> - <?= $int['cname'] ?>";
    local as routerasn;
    source address routeraddress;
    strict bind yes;
    neighbor <?= $int['address'] ?> as <?= $int['autsys'] ?>;

    <?= $t->ipproto ?> {
        # As a route collector, we want to import everything and export nothing.
        # The import filter listed here just accepts everything but adds tags.
        import where f_import_as<?= $int['autsys'] ?>();
        export none;
        <?php if( $t->router->protocol === 6 ): ?>missing lladdr ignore;<?php endif; ?>

    };

    <?php if( $int['bgpmd5secret'] && !$t->router->skip_md5 ): ?>password "<?= $int['bgpmd5secret'] ?>";<?php endif; ?>

}

<?php endforeach; ?>
