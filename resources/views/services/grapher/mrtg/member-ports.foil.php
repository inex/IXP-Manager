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
### MEMBER PORTS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $t->data['custs'] as $c ):

        $custmaxbytes = 0;
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
### MEMBER PORT: <?= $c->getFormattedName() ?>
###


<?php

        if( !isset( $t->data[ 'custports' ][ $c->id ] ) ):
            continue;
        endif;

        // individual member ports:
        foreach( $t->data[ 'custports' ][ $c->id ] as $piid ):

            $custmaxbytes += $t->data[ 'pis' ][ $piid ]->detectedSpeed() * 1000000 / 8;

            echo $this->insert(
                "services/grapher/mrtg/target", [
                    'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                    'mrtgPrefix'   => sprintf( "pi%05d", $piid ),
                    'portIds'      => [ $piid ],
                    'data'         => $t->data,
                    'graphTitle'   => sprintf( "%s -- %s -- %s -- %%s / second", $c->abbreviatedName, $t->data['pis'][$piid]->switchPort->name,
                            $t->data['pis'][$piid]->switchPort->switcher->name
                        ),
                    'directory'    => sprintf("members/%x/%05d/ints", $c->id % 16, $c->id ),
                    'maxbytes'     => $t->data['pis'][$piid]->detectedSpeed() * 1000000 / 8, // Mbps * bps / to bytes
                ]
            ) . "\n\n\n";

        endforeach;

        // individual LAG aggregates
        if( isset( $t->data['custlags'][$c->id] ) ):

            foreach( $t->data['custlags'][$c->id] as $viid => $pis ):

                $lagmaxbytes = 0;
                foreach( $pis as $piid ):
                    $lagmaxbytes += $t->data['pis'][$piid]->detectedSpeed() * 1000000 / 8;
                endforeach;

                echo $this->insert(
                    "services/grapher/mrtg/target", [
                        'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                        'mrtgPrefix'   => sprintf( "vi%05d", $viid ),
                        'portIds'      => $pis,
                        'data'         => $t->data,
                        'graphTitle'   => sprintf( "%s -- LAG Aggregate %%s / second", $c->abbreviatedName ),
                        'directory'    => sprintf( "members/%x/%05d/lags", $c->id % 16, $c->id ),
                        'maxbytes'     => $lagmaxbytes,
                    ]
                ) . "\n\n\n";

            endforeach;

        endif;

        // overall aggregate
        echo $this->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "aggregate-%05d", $c->id ),
                'portIds'      => $t->data['custports'][ $c->id ],
                'data'         => $t->data,
                'graphTitle'   => sprintf( "%s -- IXP Total Aggregate -- %%s / second", $c->abbreviatedName ),
                'directory'    => sprintf("members/%x/%05d", $c->id % 16, $c->id),
                'maxbytes'     => $custmaxbytes,
            ]
        ) . "\n\n\n";

    endforeach;
?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
