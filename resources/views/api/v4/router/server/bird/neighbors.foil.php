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
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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
?>

<?php foreach( $t->ints as $int ):

        // do not set up a session to ourselves!
        if( $int['autsys'] == $t->router->asn ):
            continue;
        endif;
?>

### AS<?= $int['autsys'] ?> - <?= $int['cname'] ?> - VLAN Interface #<?= $int['vliid'] ?>

table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;

<?php
    // It turns out that Nick's code for the prefix analysis tool actually requires a filter
    // followed by a neighbour in order. So, for now, we'll have multiple filters for customers
    // with >1 connection on the same lan
?>

filter f_import_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>

prefix set allnet;
int set allas;
{
    if !(avoid_martians()) then
            reject;

    # Route servers peering with route servers will cause the universe
    # to collapse.  Recommend evasive manoeuvers.
    if (bgp_path.first != <?= $int['autsys'] ?> ) then
            reject;

<?php
    // Only do filtering if this is enabled per client:
    $asns = [];
    $prefixes = [];
    if( $int['irrdbfilter'] ?? true ):

        $asns = \IXP\Models\Aggregators\IrrdbAggregator::asnsForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );
        if( count( $asns ) ): ?>

    allas = [ <?php echo $t->softwrap( $asns, 10, ", ", ",", 16 ); ?> ];

<?php   else: ?>

    allas = [ <?= $int['autsys'] ?> ];

<?php   endif; ?>

    if !(bgp_path.last ~ allas) then
           reject;

<?php
        $prefixes = \IXP\Models\Aggregators\IrrdbAggregator::prefixesForRouterConfiguration( $int[ 'cid' ], $t->router->protocol );
        if( count( $prefixes ) ):
        /* allnet = [ <?php echo $t->softwrap( $prefixes, 4, ", ", ",", 16 ); ?> ]; */ ?>

    allnet = [ <?= implode( ', ',
            $int['rsmorespecifics']
                    ? $t->bird()->prefixExactToLessSpecific( $prefixes, $t->router->protocol, config( 'ixp.irrdb.min_v' . $t->router->protocol . '_subnet_size' ) )
                    : $prefixes
                ) ?> ];

    if ! (net ~ allnet) then
            reject;

<?php   else: ?>

        # Deny everything because the IRR database returned nothing
        reject;

<?php   endif; ?>

<?php else: ?>

        # This ASN was configured not to use IRRDB filtering
<?php endif; ?>

    accept;
}

protocol pipe pp_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?> {
        description "Pipe for AS<?= $int['autsys'] ?> - <?= $int['cname'] ?> - VLAN Interface <?= $int['vliid'] ?>";
        table master;
        mode transparent;
        peer table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;
        import filter f_import_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;
        export where ixp_community_filter(<?= $int['autsys'] ?>);
}

protocol bgp pb_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?> from tb_rsclient {
        description "AS<?= $int['autsys'] ?> - <?= $int['cname'] ?>";
        neighbor <?= $int['address'] ?> as <?= $int['autsys'] ?>;
        import limit <?= $int['maxprefixes'] ?> action restart;
        table t_<?= $int['fvliid'] ?>_as<?= $int['autsys'] ?>;
        <?php if( $int['bgpmd5secret'] && !$t->router->skip_md5 ): ?>password "<?= $int['bgpmd5secret'] ?>";<?php endif; ?>

}

<?php endforeach; ?>
