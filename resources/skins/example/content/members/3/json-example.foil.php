<?php
// Sample member details as JSON

$data = [];

/** @var Entities\Customer $c */
foreach( $t->customers as $c ) {

    $data[ $c->getId() ]['type']           = $c->getType();
    $data[ $c->getId() ]['corpwww']        = $c->getCorpwww();
    $data[ $c->getId() ]['name']           = $c->getName();
    $data[ $c->getId() ]['autsys']         = $c->getAutsys();
    $data[ $c->getId() ]['peeringpolicy']  = $c->getPeeringpolicy();

    $data[ $c->getId() ]['routeserver']    = 'No';
    $data[ $c->getId() ]['ipv6']           = 'No';
    $data[ $c->getId() ]['ports']          = '';
    $data[ $c->getId() ]['numberofports']  = 0;
    $data[ $c->getId() ]['joined']         = $c->getDatejoin()->format( 'Y-m-d' );

    $first = true;
    foreach( $c->getVirtualInterfaces() as $vi ) {

        $pis = $vi->getPhysicalInterfaces();

        if( !count( $pis ) ) {
            continue;
        }

        $data[ $c->getId() ]['numberofports'] = $data[ $c->getId() ]['numberofports'] + count( $pis );

        $pi = $pis[0];

        if( !$first ) {
            $data[ $c->getId() ]['ports'] .= ' + ';
        }

        if( count( $pis ) > 1 ) {
            $data[ $c->getId() ]['ports'] .= $data[ $c->getId() ]['ports'] . count( $pis ) . '*';
        }

        $data[ $c->getId() ]['ports'] .= $pi->resolveSpeed();

        $first = false;

        foreach( $vi->getVlanInterfaces() as $vli ) {
            if( $vli->getRsclient() ) {
                $data[ $c->getId() ]['routeserver'] = 'Yes';
            }

            if( $vli->getIpv6enabled() ) {
                $data[ $c->getId() ][ 'ipv6' ] = 'Yes';

            }
        }
    }

}

echo json_encode( $data, JSON_PRETTY_PRINT );
