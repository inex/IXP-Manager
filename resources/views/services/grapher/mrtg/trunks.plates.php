<?php

    // MRTG Configuration Templates
    //
    // This is v4 maintaining legacy support for manual trunk graphs.
    // Please see: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs
    //
    // This will be phased out / automated / managed on the frontend soon
    //
    // You should not need to edit these files - instead use your own custom skins. If
    // you can't effect the changes you need with skinning, consider posting to the mailing
    // list to see if it can be achieved / incorporated.
    //
    // Skinning: https://ixp-manager.readthedocs.org/en/latest/features/skinning.html

?>

# Manually insert a bunch of definitions here for your IXP's trunk links
# between switches.  There is really no way to be able to create a sane
# definition of these in the IXP Manager database, so we chicken out and let
# each IXP do it manually.  Simplicity r00lz.
#
# See: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs#inter-switch-link-graphs
#
# Here are some examples.

## degkcp-ixdub1 - LAN1
#Target[core-degkcp-ixdub1-lan1]: #ethernet23:sillypassword@swi1-deg1-2:::::2 + #ethernet24:sillypassword@swi1-deg1-2:::::2
#MaxBytes[core-degkcp-ixdub1-lan1]: 2500000000
#Directory[core-degkcp-ixdub1-lan1]: trunks
#Title[core-degkcp-ixdub1-lan1]: Trunk Core - DEGKCP-IXDUB1 - LAN1

## tcydub1-ixdub1 - LAN1
#Target[core-tcydub1-ixdub1-lan1]: #ethernet23:sillypassword@swi1-ix1-1:::::2 + #ethernet24:sillypassword@swi1-ix1-1:::::2
#MaxBytes[core-tcydub1-ixdub1-lan1]: 2500000000
#Directory[core-tcydub1-ixdub1-lan1]: trunks
#Title[core-tcydub1-ixdub1-lan1]: Trunk Core - TCYDUB1-IXDUB1 - LAN1


## swi1-ix1-1 - swi1-ix1-2
#Target[core-swi1-ix1-1_swi1-ix1-2-lan1]: #ethernet21:sillypassword@swi1-ix1-1:::::2 + #ethernet22:sillypassword@swi1-ix1-1:::::2
#MaxBytes[core-swi1-ix1-1_swi1-ix1-2-lan1]: 2500000000
#Directory[core-swi1-ix1-1_swi1-ix1-2-lan1]: trunks
#Title[core-swi1-ix1-1_swi1-ix1-2-lan1]: Inter-POP Trunk Core - IXDUB1 - swi1-ix1-1 swi1-ix1-2 - LAN1
