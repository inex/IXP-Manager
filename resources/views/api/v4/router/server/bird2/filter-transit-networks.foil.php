

########################################################################################
########################################################################################
#
# Filter known transit networks
#
# Inspired by: http://bgpfilterguide.nlnog.net/guides/no_transit_leaks/
#
########################################################################################
########################################################################################

<?php
    // default transit networks to block
    $no_transit_asns = [
        174   => 'Cogent',
        701   => 'UUNET',
        1299  => 'Telia',
        2914  => 'NTT Communications',
        3257  => 'GTT Backbone',
        3320  => 'Deutsche Telekom AG (DTAG)',
        3356  => 'Level3',
        3491  => 'PCCW',
        4134  => 'Chinanet',
        5511  => 'Orange opentransit',
        6453  => 'Tata Communications',
        6461  => 'Zayo Bandwidth',
        6762  => 'Seabone / Telecom Italia',
        6830  => 'Liberty Global',
        7018  => 'AT&T',
    ];

    // possible overrides - exclusions from the above:
    if( count( config( 'ixp.no_transit_asns.exclude' ) ) ) {
        foreach( config( 'ixp.no_transit_asns.exclude' ) as $asn ) {
            if( isset( $no_transit_asns[$asn] ) ) {
                unset( $no_transit_asns[$asn] );
            }
        }
    }

    // possible overrides - complete replacement:
    if( config( 'ixp.no_transit_asns.override' ) !== false ) {
        $no_transit_asns = [];
        foreach( config( 'ixp.no_transit_asns.override' ) as $asn ) {
            $no_transit_asns[ $asn ] = 'Override from .env file';
        }
    }
?>

# Filtering the following ASNs:
#
<?php foreach( $no_transit_asns as $asn => $desc ): ?>
# <?= $asn ?> - <?= $desc ?>

<?php endforeach; ?>

<?php if( count( $no_transit_asns ) === 0 ): ?>
# .env file has disabled transit ASN filtering with an empty IXP_NO_TRANSIT_ASNS_OVERRIDE setting:
function filter_has_transit_path()
{
    return false;
}

<?php else: ?>
define TRANSIT_ASNS = [ <?= implode( ', ', array_keys( $no_transit_asns ) ) ?> ];

function filter_has_transit_path()
int set transit_asns;
{
    transit_asns = TRANSIT_ASNS;
    if (bgp_path ~ transit_asns) then {
        bgp_large_community.add( IXP_LC_FILTERED_TRANSIT_FREE_ASN );
        return true;
    }

    return false;
}

<?php endif; ?>