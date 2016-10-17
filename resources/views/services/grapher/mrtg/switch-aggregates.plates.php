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
### SWITCH AGGREGATE GRAPHS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $data['sws'] as $switchid => $switch ):

        if( !isset( $data['swports'][$switch->getId()] ) ):
            continue;
        endif;

        $this->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "switch-aggregate-%05d", $switch->getId() ),
                'portIds'      => $data['swports'][$switch->getId()],
                'data'         => $data,
                'graphTitle'   => sprintf( config('identity.orgname') . " - Peering %%s / second on %s", $switch->getName() ),
                'directory'    => sprintf( "switches/%03d", $switchid ),
            ]
        );

    endforeach;
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
