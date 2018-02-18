<?php /*
    MRTG Configuration Templates

    INEX Switch Trunk Graphs - a production example.

    Note that we created an additional config file called config/custom.php where you can
    place your own customised config options. As all our production switches use the same
    SNMP password, we set it there and use it below.

    These graph definitions match up with config/grapher_trunks.php

*/ ?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
###
###
### TRUNKS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################


################
#    LAN1      #
################

# degkcp-tcydub1 - LAN1 - Primary
Target[core-degkcp-tcydub1-lan1]: #Ethernet51/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan1]: 12500000000
Directory[core-degkcp-tcydub1-lan1]: trunks
Title[core-degkcp-tcydub1-lan1]: Trunk Core - DEGKCP-TCYDUB1 - LAN1 - Primary

# tcydub1-tcydub1 - LAN1 - Primary
Target[core-tcydub1-tcydub1-lan1]: #Ethernet51/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-tcydub1-tcydub1-lan1]: 12500000000
Directory[core-tcydub1-tcydub1-lan1]: trunks
Title[core-tcydub1-tcydub1-lan1]: Trunk Core - TCYDUB1 Internal - LAN1 - Primary


# cwt1 internal link swi1-cwt1-1 -> -2
Target[core-cwt1-int1-lan1]: #Ethernet53/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-cwt1-int1-lan1]: 12500000000
Directory[core-cwt1-int1-lan1]: trunks
Title[core-cwt1-int1-lan1]: Trunk Core - TCYDUB1 Internal - LAN1 - Primary

# kcp1 internal link swi1-kcp1-1 -> -2
Target[core-kcp1-int1-lan1]: #Ethernet53/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-kcp1-int1-lan1]: 12500000000
Directory[core-kcp1-int1-lan1]: trunks
Title[core-kcp1-int1-lan1]: Trunk Core - KCP1 Internal - LAN1 - Primary


# kcp1-pwt1 - LAN1
Target[core-kcp1-pwt1-lan1]: #Ethernet51/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-2.mgmt.inex.ie:::::2
MaxBytes[core-kcp1-pwt1-lan1]: 12500000000
Directory[core-kcp1-pwt1-lan1]: trunks
Title[core-kcp1-pwt1-lan1]: Trunk Core - KCP1 - PWT1 - LAN1

# cwt1-pwt1 - LAN1
Target[core-cwt1-pwt1-lan1]: #Ethernet51/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-2.mgmt.inex.ie:::::2
MaxBytes[core-cwt1-pwt1-lan1]: 12500000000
Directory[core-cwt1-pwt1-lan1]: trunks
Title[core-cwt1-pwt1-lan1]: Trunk Core - TCYDUB1-IXDUB1 - LAN1

# swi1-deg1-1 - swi1-deg1-3
Target[core-swi1-deg1-1_swi1-deg1-3-lan1]: #Ethernet33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-2.mgmt.inex.ie:::::2
					+ #Ethernet34:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-2.mgmt.inex.ie:::::2
MaxBytes[core-swi1-deg1-1_swi1-deg1-3-lan1]: 2500000000
Directory[core-swi1-deg1-1_swi1-deg1-3-lan1]: trunks
Title[core-swi1-deg1-1_swi1-deg1-3-lan1]: Inter-POP Trunk Core - DEGKCP - swi1-deg1-1 swi1-deg1-3 - LAN1


# swi1-cwt1-2 - swi1-cwt1-3
Target[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: #Ethernet33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-2.mgmt.inex.ie:::::2
					+ #Ethernet34:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-2.mgmt.inex.ie:::::2
MaxBytes[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: 2500000000
Directory[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: trunks
Title[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: Intra-POP Trunk Core - TCYDUB1 - swi1-cwt1-2 swi1-cwt1-3 - LAN1


# swi1-cwt1-1 - swi1-cls1-1
Target[core-cwt1-cls1-lan1]: #Ethernet41:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet42:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet43:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet44:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet45:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet46:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet47:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
                                        + #Ethernet48:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-cwt1-cls1-lan1]: 10000000000
Directory[core-cwt1-cls1-lan1]: trunks
Title[core-cwt1-cls1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy1-1 swi1-vfw-1 - LAN1

# swi1-deg1-1 - swi1-tcy3-1
Target[core-kcp1-nwb1-lan1]: #Ethernet52/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-kcp1-2.mgmt.inex.ie:::::2
MaxBytes[core-kcp1-nwb1-lan1]: 12500000000
Directory[core-kcp1-nwb1-lan1]: trunks
Title[core-kcp1-nwb1-lan1]: Inter-POP Trunk Core - Telecity NWBP - swi1-deg1-1 swi1-tcy3-1 - LAN1

# nwb1 -> cls1
Target[core-nwb1-cls1-lan1]: #Ethernet33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet34:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet35:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet36:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet37:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet38:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet39:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
                            + #Ethernet40:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-nwb1-1.mgmt.inex.ie:::::2
MaxBytes[core-nwb1-cls1-lan1]: 10000000000
Directory[core-nwb1-cls1-lan1]: trunks
Title[core-nwb1-cls1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy3-1 swi1-vfw-1 - LAN1

# cwt1 -> nwb1
Target[core-cwt1-nwb1-lan1]: #Ethernet52/1:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-cwt1-2.mgmt.inex.ie:::::2
MaxBytes[core-cwt1-nwb1-lan1]: 12500000000
Directory[core-cwt1-nwb1-lan1]: trunks
Title[core-cwt1-nwb1-lan1]: Inter-POP Trunk Core - swi1-cwt1-2 - swi1-nwb1-1 - LAN1


################
#    LAN2      #
################

# degkcp-tcydub1 - LAN2
Target[core-degkcp-tcydub1-lan2]: #1\:37:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
				+ #1\:38:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
				+ #1\:39:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
				+ #1\:40:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan2]: 5000000000
Directory[core-degkcp-tcydub1-lan2]: trunks
Title[core-degkcp-tcydub1-lan2]: Trunk Core - DEGKCP-TCYDUB1 - LAN2

# degkcp-ixdub1 - LAN2
Target[core-degkcp-ixdub1-lan2]: #1\:31:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
                               + #1\:32:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-degkcp-ixdub1-lan2]: 2500000000
Directory[core-degkcp-ixdub1-lan2]: trunks
Title[core-degkcp-ixdub1-lan2]: Trunk Core - DEGKCP-IXDUB1 - LAN2

# tcydub1-ixdub1 - LAN2
Target[core-tcydub1-ixdub1-lan2]: #1\:33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-cwt1-1.mgmt.inex.ie:::::2
                                + #1\:34:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-tcydub1-ixdub1-lan2]: 2500000000
Directory[core-tcydub1-ixdub1-lan2]: trunks
Title[core-tcydub1-ixdub1-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2

# degkcp-tcy3 - LAN2
Target[core-degkcp-tcy3-lan2]: #1\:43:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-degkcp-tcy3-lan2]: 1250000000
Directory[core-degkcp-tcy3-lan2]: trunks
Title[core-degkcp-tcy3-lan2]: Trunk Core - DEGKCP-TCY3 - LAN2

# tcydub1-vfw - LAN2
Target[core-tcydub1-vfw-lan2]: #1\:44:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-tcydub1-vfw-lan2]: 1250000000
Directory[core-tcydub1-vfw-lan2]: trunks
Title[core-tcydub1-vfw-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2

# tcy3-vfw - LAN2
Target[core-tcydub3-vfw-lan2]: #1\:33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-nwb1-1.mgmt.inex.ie:::::2
MaxBytes[core-tcydub3-vfw-lan2]: 1250000000
Directory[core-tcydub3-vfw-lan2]: trunks
Title[core-tcydub3-vfw-lan2]: Trunk Core - TCYDUB3-VFW - LAN2

# ix1 - ix2 - LAN2
Target[core-ix1-ix2-lan2]: #1\:45:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-pwt1-1.mgmt.inex.ie:::::2
			+ #1\:46:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-pwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-ix1-ix2-lan2]: 2500000000
Directory[core-ix1-ix2-lan2]: trunks
Title[core-ix1-ix2-lan2]: Trunk Core - Interxion DUB1 to DUB2 - LAN2


# swi2-deg1-1 - swi2-deg1-4
Target[core-swi2-deg1-2_swi2-deg1-4-lan2]: #1\:47:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
                                         + #1\:48:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-kcp1-1.mgmt.inex.ie:::::2
MaxBytes[core-swi2-deg1-2_swi2-deg1-4-lan2]: 2500000000
Directory[core-swi2-deg1-2_swi2-deg1-4-lan2]: trunks
Title[core-swi2-deg1-2_swi2-deg1-4-lan2]: Inter-POP Trunk Core - DEGKCP - swi2-deg1-1 swi2-deg1-4 - LAN2


# swi2-tcy1-2 - swi2-tcy1-4
Target[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: #1\:43:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-cwt1-1.mgmt.inex.ie:::::2
MaxBytes[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: 1250000000
Directory[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: trunks
Title[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: Inter-POP Trunk Core - TCYDUB1 - swi2-tcy1-2 swi2-tcy1-4 - LAN2
