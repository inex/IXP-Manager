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

    ########################################################################################
    ########################################################################################
    #
    # UI Based Filtering - export rules
    #
    ########################################################################################
    ########################################################################################

<?php
/**
 * $t->int contains:
 *
 *         [cid] => 999
 *         [cname] => Customer Name
 *         [cshortname] => shortname
 *         [autsys] => 65000
 *         [peeringmacro] => QWE              // or AS65500 if not defined
 *         [vliid] => 159
 *         [fvliid] => 00159                  // formatted %05d
 *         [address] => 192.0.2.123
 *         [bgpmd5secret] => qwertyui         // or false
 *         [as112client] => 1                 // if the member is an as112 client or not
 *         [rsclient] => 1                    // if the member is a route server client or not
 *         [maxprefixes] => 20
 *         [irrdbfilter] => 0/1               // if IRRDB filtering should be applied
 *         [rsmorespecifics] => 0/1           // if IRRDB filtering should allow more specifics
 *         [location_name] => Interxion DUB1
 *         [location_shortname] => IX-DUB1
 *         [location_tag] => ix1
 *
 *
 *
 */

// Get all filters for this customer:
$filters = \IXP\Models\RouteServerFilter::whereCustomerId( $t->int['cid'] )
    ->where( 'enabled', 1 )
    ->where(function ($query) use ($t) {
        $query->whereNull( 'vlan_id' )
            ->orWhere( 'vlan_id', $t->int['vlanid'] );
    })
    ->where(function ($query) use ($t) {
        $query->whereNull( 'protocol' )
            ->orWhere( 'protocol', $t->router->protocol );
    })
    ->orderBy('order_by')
    ->get();



foreach( $filters as $filter ) {

    $indent = '    ';
    echo "\n";
    echo "    # Filter id:{$filter->id} created:{$filter->created_at} updated:{$filter->created_at}\n";
    if( $filter->peer_id ):
    $indent .= '    '; ?>    if ( bgp_path.first = <?= \IXP\Models\Customer::find( $filter->peer_id )->autsys ?> ) then {
<?php endif;

    if( $filter->received_prefix ):
echo $indent; ?>if ( net = <?= $filter->received_prefix ?> ) then {
<?php
    $indent .= '    ';
    endif;

    switch( $filter->action_receive ):

        case 'AS_IS':
            echo "{$indent}return true;\n";
            break;

        case 'NO_ADVERTISE':
            echo "{$indent}return false;\n";
            break;

        case 'PREPEND_THRICE':
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}return true;\n";
            break;

        case 'PREPEND_TWICE':
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}return true;\n";
            break;

        case 'PREPEND_ONCE':
            echo "{$indent}bgp_path.prepend( bgp_path.first );\n";
            echo "{$indent}return true;\n";
            break;

    endswitch;


    if( $filter->received_prefix ):
    $indent = substr( $indent, 0, -4 );
    echo $indent;
?>}
<?php endif;

if( $filter->peer_id ):
    $indent = substr( $indent, 0, -4 );
    echo $indent;
?>}
<?php endif;

} //foreach
?>

    ########################################################################################
    # End UI Based Filtering - export rules
    ########################################################################################
