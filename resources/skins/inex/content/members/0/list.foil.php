<?php
// INEX member list for https://www.inex.ie/

echo Cache::remember('skin-inex-content-members-list.json', '14400', function() use ($t) {
    $data = [];

    /** @var Entities\Customer $c */
    foreach( $t->customers as $c ) {

        $data[ $c->getId() ][ 'type' ] = $c->resolveType();
        $data[ $c->getId() ][ 'corpwww' ] = $c->getCorpwww();
        $data[ $c->getId() ][ 'name' ] = $c->getName();
        $data[ $c->getId() ][ 'numberofports' ] = 0;
        $data[ $c->getId() ][ 'joined' ] = $c->getDatejoin()->format( 'Y-m-d' );

        if( $l = $c->getLogo() ) {
            $data[ $c->getId() ][ 'logo' ] = url( '' ) . '/logos/' . $l->getShardedPath();
        } else {
            $data[ $c->getId() ][ 'logo' ] = false;
        }

        if( !$c->isTypeAssociate() ) {
            $data[ $c->getId() ][ 'autsys' ] = $c->getAutsys();
            $data[ $c->getId() ][ 'peeringpolicy' ] = $c->getPeeringpolicy();

            $data[ $c->getId() ][ 'routeserver' ] = 'No';
            $data[ $c->getId() ][ 'ipv4' ] = 'No';
            $data[ $c->getId() ][ 'ipv6' ] = 'No';
            $data[ $c->getId() ][ 'ports' ] = '';
        }

        $first = true;
        foreach( $c->getVirtualInterfaces() as $vi ) {

            if( $vi->isTypeCore() ) {
                continue;
            }

            $pis = $vi->getPhysicalInterfaces();

            if( !count( $pis ) ) {
                continue;
            }

            $data[ $c->getId() ][ 'numberofports' ] = $data[ $c->getId() ][ 'numberofports' ] + count( $pis );

            $pi = $pis[ 0 ];

            if( !$first ) {
                $data[ $c->getId() ][ 'ports' ] .= ' + ';
            }

            if( count( $pis ) > 1 ) {
                $data[ $c->getId() ][ 'ports' ] .= $data[ $c->getId() ][ 'ports' ] . count( $pis ) . '*';
            }

            $data[ $c->getId() ][ 'ports' ] .= $pi->speed();

            $first = false;

            foreach( $vi->getVlanInterfaces() as $vli ) {
                if( $vli->getRsclient() ) {
                    $data[ $c->getId() ][ 'routeserver' ] = 'Yes';
                }

                if( $vli->getIpv4enabled() ) {
                    $data[ $c->getId() ][ 'ipv4' ] = 'Yes';

                }

                if( $vli->getIpv6enabled() ) {
                    $data[ $c->getId() ][ 'ipv6' ] = 'Yes';

                }
            }
        }

    }

    return json_encode( $data, JSON_PRETTY_PRINT );
});

