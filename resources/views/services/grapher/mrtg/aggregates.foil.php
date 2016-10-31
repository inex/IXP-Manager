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
    foreach( $t->data['infras'] as $infraid => $infra ):

        if( !isset( $t->data['infraports'][$infra->getId()] ) ):
            continue;
        endif;

        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "ixp%03d_infra%03d", $t->ixp->getId(), $infra->getId() ),
                'portIds'      => $t->data['infraports'][$infra->getId()],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " %%s / second on %s", $infra->getName() ),
                'directory'    => sprintf( "infras/%03d", $infraid ),
            ]
        );
        
        echo "\n\n\n";

    endforeach; // foreach( $t->data['infras'] as $infraid => $infra ):
?>


<?php
    if( isset( $t->data['ixpports'] ) ):
        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "ixp%03d", $t->ixp->getId() ),
                'portIds'      => $t->data['ixpports'],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " - %%s / second" ),
                'directory'    => sprintf( "ixp" ),
            ]
        );
    endif;
?>



#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
