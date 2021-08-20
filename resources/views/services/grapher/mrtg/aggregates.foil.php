<?php /*
    MRTG Configuration Templates

    Please see: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs

    You should not need to edit these files - instead use your own custom skins. If
    you can't effect the changes you need with skinning, consider posting to the mailing
    list to see if it can be achieved / incorporated.

    Skinning: https://github.com/inex/IXP-Manager/wiki/Skinning */
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
###
###
### AGGREGATE GRAPHS
###
### Source:    <?php echo $t->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $t->data[ 'infras' ] as $infraid => $infra ):

        if( !isset( $t->data[ 'infraports' ][ $infra->id ] ) ):
            continue;
        endif;

        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "ixp%03d-infra%03d", 1, $infra->id ),
                'portIds'      => $t->data['infraports'][ $infra->id ],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " %%s / second on %s", $infra->name ),
                'directory'    => sprintf( "infras/%03d", $infraid ),
                'maxbytes'     => $t->data['infraports_maxbytes'][ $infraid ],
            ]
        );

    endforeach; // foreach( $t->data['infras'] as $infraid => $infra ):
?>


<?php
    if( isset( $t->data['ixpports'] ) ):
        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "ixp%03d", 1 ),
                'portIds'      => $t->data['ixpports'],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " - %%s / second" ),
                'directory'    => sprintf( "ixp" ),
                'maxbytes'     => $t->data['ixpports_maxbytes'],
            ]
        );
    endif;
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
