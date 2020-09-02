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
    foreach( $t->data['sws'] as $switchid => $switch ):

        if( !isset( $t->data[ 'swports' ][ $switch->id ] ) ):
            continue;
        endif;

        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "switch-aggregate-%05d", $switch->id ),
                'portIds'      => $t->data[ 'swports' ][ $switch->id ],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " - Peering %%s / second on %s", $switch->name ),
                'directory'    => sprintf( "switches/%03d", $switchid ),
                'maxbytes'     => $t->data[ 'swports_maxbytes' ][ $switchid ],
            ]
        );

    endforeach;
?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
