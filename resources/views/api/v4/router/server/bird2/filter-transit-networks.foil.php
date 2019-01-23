

########################################################################################
########################################################################################
#
# Filter known transit networks
#
# Inspired by: http://bgpfilterguide.nlnog.net/guides/no_transit_leaks/
#
########################################################################################
########################################################################################


define TRANSIT_ASNS = [ 174,                  # Cogent
                        209,                  # Qwest (HE carries this on IXPs IPv6 (Jul 12 2018))
                        701,                  # UUNET
                        702,                  # UUNET
                        1239,                 # Sprint
                        1299,                 # Telia
                        2914,                 # NTT Communications
                        3257,                 # GTT Backbone
                        3320,                 # Deutsche Telekom AG (DTAG)
                        3356,                 # Level3
                        3549,                 # Level3
                        3561,                 # Savvis / CenturyLink
                        4134,                 # Chinanet
                        5511,                 # Orange opentransit
                        6453,                 # Tata Communications
                        6461,                 # Zayo Bandwidth
                        6762,                 # Seabone / Telecom Italia
                        7018 ];               # AT&T

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
