
    # From: api/v4/router/collector/bird2/import-pre-extra)
    # 
    # We (INEX) uses this to tag routes learnt from our own 
    # route servers with an information community and accept
    # them as is.

    if( 43760 = <?= $t->int['autsys'] ?> ) then {
        bgp_large_community.add( IXP_LC_INFO_FROM_IXROUTESERVER );
        return true;
    }

