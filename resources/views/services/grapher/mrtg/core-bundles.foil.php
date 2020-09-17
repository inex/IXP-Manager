<?php /*
    MRTG Configuration Templates

    Please see: https://docs.ixpmanager.org/grapher/mrtg/

    You should not need to edit these files - instead use your own custom skins. If
    you can't effect the changes you need with skinning, consider posting to the mailing
    list to see if it can be achieved / incorporated.

    Skinning: https://docs.ixpmanager.org/features/skinning/
*/ ?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
###
###
### Core Bundle Ports 
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $t->data['cbs'] as $cb ):

        $cbmaxbytes = 0;
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
### Core Bundle: <?= $cb->description . "\n" ?>
###


<?php
        if( !isset( $t->data['cbports'][$cb->id] ) ):
            continue;
        endif;

        // individual Core Bundle links:
        foreach( $t->data[ 'cbports' ][ $cb->id ] as $clid ):

            foreach( [ 'sidea', 'sideb' ] as $side ):

                $piid = $clid[$side];
                
                $cbmaxbytes += $t->data['pis'][$piid]->detectedSpeed() * 1000000 / 8;

                echo $this->insert(
                    "services/grapher/mrtg/target", [
                        'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                        'mrtgPrefix'   => sprintf( "cl%05d-%s", $piid, $side ),
                        'portIds'      => [ $piid ],
                        'data'         => $t->data,
                        'graphTitle'   => sprintf( "%s -- %s -- %s -- %s -- %%s / second",
                                $t->data['cbs'][$cb->id]->graph_title,
                                ( $side === 'sidea' ) ? 'Side A' : 'Side B',
                                $t->data['pis'][$piid]->switchPort->name,
                                $t->data['pis'][$piid]->switchPort->switcher->name
                            ),
                        'directory'    => sprintf("corebundles/%05d/ints", $cb->id),
                        'maxbytes'     => $t->data['pis'][ $piid ]->detectedSpeed() * 1000000 / 8, // Mbps * bps / to bytes
                        ]
                ) . "\n\n\n";

            endforeach;

        endforeach;

        // aggregates
        foreach( [ 'sidea', 'sideb' ] as $side ):
            echo $this->insert(
                "services/grapher/mrtg/target", [
                    'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                    'mrtgPrefix'   => sprintf( "cb-aggregate-%05d-%s", $cb->id, $side ),
                    'portIds'      => $t->data[ 'cbbundles' ][ $cb->id ][ $side ],
                    'data'         => $t->data,
                    'graphTitle'   => sprintf( "%s -- %s -- %%s / second",
                        $t->data['cbs'][ $cb->id ]->graph_title,
                        ( $side === 'sidea' ) ? 'Side A' : 'Side B'
                    ),
                    'directory'    => sprintf("corebundles/%05d", $cb->id ),
                    'maxbytes'     => $cbmaxbytes,
                    ]
            ) . "\n\n\n";

        endforeach;

    endforeach;
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
