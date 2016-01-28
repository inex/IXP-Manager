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
    foreach( $custs as $c ):

        $custPortsAggregateSpeed = 0;
        $custPorts = [];

        foreach( $c->getVirtualInterfaces() as $vi ):

            $custLagPortsAggregateSpeed = 0;
            $custLagPorts = [];

            foreach( $vi->getPhysicalInterfaces() as $pi ):

                $mrtgId                     = $pi->getSwitchPort()->ifnameToSNMPIdentifier();
                $custPorts[]                = $mrtgId;
                $custLagPorts[]             = $mrtgId;
                $custPortsAggregateSpeed    += $pi->getSpeed();
                $custLagPortsAggregateSpeed += $pi->getSpeed();

                $this->insert('services/grapher/mrtg/member-port' , ['c' => $c, 'pi' => $pi, 'mrtgId' => $mrtgId]);

            endforeach;

        // add an aggregate for LAG ports
        if( count( $vi->getPhysicalInterfaces() ) > 1 ):
            $this->insert('services/grapher/mrtg/member-lag-port' , ['portsByInfrastructure' => $portsByInfrastructure, 'ixp' => $ixp]);
        endif;

    endforeach;

    $this->insert('services/grapher/mrtg/member-aggregate-port' , ['portsByInfrastructure' => $portsByInfrastructure, 'ixp' => $ixp]);

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

endforeach;
