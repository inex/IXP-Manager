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
    foreach( $data['custs'] as $c ):

?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
### MEMBER PORT: <?= $c->getFormattedName() ?>
###


<?php

        if( !isset( $data['custports'][$c->getId()] ) ):
            continue;
        endif;

        // individual member ports:
        foreach( $data['custports'][$c->getId()] as $piid ):

            $this->insert(
                "services/grapher/mrtg/target", [
                    'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                    'mrtgPrefix'   => sprintf( "pi%05d", $piid ),
                    'portIds'      => [ $piid ],
                    'data'         => $data,
                    'graphTitle'   => sprintf( "%s -- %s -- %s -- %%s / second", $c->getAbbreviatedName(), $data['pis'][$piid]->getSwitchPort()->getName(),
                            $data['pis'][$piid]->getSwitchPort()->getSwitcher()->getName()
                        ),
                    'directory'    => sprintf("members/%x/%05d/ints", $c->getId() % 16, $c->getId()),
                ]
            );

        endforeach;

        // individual LAG aggregates
        if( isset( $data['custlags'][$c->getId()] ) ):

            foreach( $data['custlags'][$c->getId()] as $viid => $pis ):

                $this->insert(
                    "services/grapher/mrtg/target", [
                        'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                        'mrtgPrefix'   => sprintf( "vi%05d", $viid ),
                        'portIds'      => $pis,
                        'data'         => $data,
                        'graphTitle'   => sprintf( "%s -- LAG Aggregate %%s / second", $c->getAbbreviatedName() ),
                        'directory'    => sprintf( "members/%x/%05d/lags", $c->getId() % 16, $c->getId() ),
                    ]
                );

            endforeach;

        endif;

        // overall aggregate
        $this->insert(
            "services/grapher/mrtg/target", [
                'trafficTypes' => \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES,
                'mrtgPrefix'   => sprintf( "aggregate-%05d", $c->getId() ),
                'portIds'      => $data['custports'][$c->getId()],
                'data'         => $data,
                'graphTitle'   => sprintf( "%s -- IXP Total Aggregate -- %%s / second", $c->getAbbreviatedName() ),
                'directory'    => sprintf("members/%x/%05d", $c->getId() % 16, $c->getId()),
            ]
        );

    endforeach;
?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
