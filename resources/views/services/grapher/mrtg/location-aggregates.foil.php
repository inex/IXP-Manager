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
### LOCATION AGGREGATE GRAPHS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $t->data['locs'] as $locationid => $location ):

        if( !isset( $t->data[ 'locports' ][ $location->id ] ) ):
            continue;
        endif;

        echo $t->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "location-aggregate-%05d", $location->id ),
                'portIds'      => $t->data[ 'locports' ][ $location->id ],
                'data'         => $t->data,
                'graphTitle'   => sprintf( config('identity.orgname') . " - Peering %%s / second on %s", $location->name ),
                'directory'    => sprintf( "locations/%03d", $locationid ),
                'maxbytes'     => $t->data[ 'locports_maxbytes' ][ $locationid ],
            ]
        );

    endforeach;
?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
