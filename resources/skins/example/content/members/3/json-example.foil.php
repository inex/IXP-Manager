<?php
// Sample member details as JSON

$data = [];

/** @var \IXP\Models\Customer $c */
foreach( $t->customers as $c ) {
    $data[ $c->id ]['type']           = $c->type;
    $data[ $c->id ]['corpwww']        = $c->corpwww;
    $data[ $c->id ]['name']           = $c->name;
    $data[ $c->id ]['autsys']         = $c->autsys;
    $data[ $c->id ]['peeringpolicy']  = $c->peeringpolicy;

    $data[ $c->id ]['routeserver']    = 'No';
    $data[ $c->id ]['ipv6']           = 'No';
    $data[ $c->id ]['ports']          = '';
    $data[ $c->id ]['numberofports']  = 0;
    $data[ $c->id ]['joined']         = \Carbon\Carbon::parse( $c->datejoin )->format( 'Y-m-d' );

    $first = true;
    foreach( $c->virtualInterfaces as $vi ) {
        $pis = $vi->physicalInterfaces;

        if( !count( $pis ) ) {
            continue;
        }

        $data[ $c->id ]['numberofports'] = $data[ $c->id ]['numberofports'] + count( $pis );

        $pi = $pis[0];

        if( !$first ) {
            $data[ $c->id ]['ports'] .= ' + ';
        }

        if( count( $pis ) > 1 ) {
            $data[ $c->id ]['ports'] .= $data[ $c->id ]['ports'] . count( $pis ) . '*';
        }

        $data[ $c->id ]['ports'] .= $pi->speed();

        $first = false;

        foreach( $vi->vlanInterfaces as $vli ) {
            if( $vli->rsclient ) {
                $data[ $c->id ]['routeserver'] = 'Yes';
            }

            if( $vli->ipv6enabled ) {
                $data[ $c->id ][ 'ipv6' ] = 'Yes';
            }
        }
    }
}

echo json_encode( $data, JSON_PRETTY_PRINT );
