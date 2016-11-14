<?php
    // MRTG Configuration Templates
    //
    // INEX Switch Trunk Graphs - a production example.
    //
    // This is v4 maintaining legacy support for manual trunk graphs.
    // Please see: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs
    //
    // This will be phased out / automated / managed on the frontend soon
    //
    // Note that we created an additional coinfig/grapher.php variable called backends.mrtg.snmppasswd and
    // all our production switches use the same SNMP password. This variable is then available as
    // <?php echo $snmppasswd ? >.
    //
    // Our application.ini for these graphs is as follows:
    //
    // mrtg.conf.inex.snmppasswd = 'xxxxxxx'
    //
    // mrtg.trunk_graphs[] = "1::core-degkcp-tcydub1-lan1::DEG Kilcarbery Park to Telecity DUB1 (LAN1)"
    // mrtg.trunk_graphs[] = "1::core-degkcp-ixdub1-lan1::DEG Kilcarbery Park to Interxion DUB1 (LAN1)"
    // mrtg.trunk_graphs[] = "1::core-tcydub1-ixdub1-lan1::Telecity DUB1 to Interxion DUB1 (LAN1)"
    //
    // mrtg.trunk_graphs[] = "1::core-degkcp-tcydub1-lan2::DEG Kilcarbery Park to Telecity DUB1 (LAN2)"
    // mrtg.trunk_graphs[] = "1::core-degkcp-ixdub1-lan2::DEG Kilcarbery Park to Interxion DUB1 (LAN2)"
    // mrtg.trunk_graphs[] = "1::core-tcydub1-ixdub1-lan2::Telecity DUB1 to Interxion DUB1 (LAN2)"
?>


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
Target[core-degkcp-tcydub1-lan1]: #1\:33:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:34:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:35:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:36:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:37:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
 	 			+ #1\:38:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:39:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:40:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan1]: 9000000000
Directory[core-degkcp-tcydub1-lan1]: trunks
Title[core-degkcp-tcydub1-lan1]: Trunk Core - DEGKCP-TCYDUB1 - LAN1 - Primary

# degkcp-tcydub1 - LAN1 - Secondary
Target[core-degkcp-tcydub1-lan1-sec]: #1\:41:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:42:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:43:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:44:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:45:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:46:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:47:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:48:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan1-sec]: 9000000000
Directory[core-degkcp-tcydub1-lan1-sec]: trunks
Title[core-degkcp-tcydub1-lan1-sec]: Trunk Core - DEGKCP-TCYDUB1 - LAN1 - Secondary

# tcydub1-tcydub1 - LAN1 - Primary
Target[core-tcydub1-tcydub1-lan1]: #1\:49:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:53:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-tcydub1-tcydub1-lan1]: 9000000000
Directory[core-tcydub1-tcydub1-lan1]: trunks
Title[core-tcydub1-tcydub1-lan1]: Trunk Core - TCYDUB1 Internal - LAN1 - Primary


# degkcp-ixdub1 - LAN1
Target[core-degkcp-ixdub1-lan1]: #1\:25:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:26:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:27:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-ixdub1-lan1]: 3750000000
Directory[core-degkcp-ixdub1-lan1]: trunks
Title[core-degkcp-ixdub1-lan1]: Trunk Core - TCYKCP-IXDUB1 - LAN1

# tcydub1-ixdub1 - LAN1
Target[core-tcydub1-ixdub1-lan1]: #1\:25:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:26:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:27:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-tcydub1-ixdub1-lan1]: 3750000000
Directory[core-tcydub1-ixdub1-lan1]: trunks
Title[core-tcydub1-ixdub1-lan1]: Trunk Core - TCYDUB1-IXDUB1 - LAN1

# swi1-ix1-1 - swi1-ix1-2
Target[core-swi1-ix1-1_swi1-ix1-2-lan1]: #1\:45:<?php echo $snmppasswd ?>@swi1-ix1-1.inex.ie:::::2
					 + #1\:46:<?php echo $snmppasswd ?>@swi1-ix1-1.inex.ie:::::2
MaxBytes[core-swi1-ix1-1_swi1-ix1-2-lan1]: 2500000000
Directory[core-swi1-ix1-1_swi1-ix1-2-lan1]: trunks
Title[core-swi1-ix1-1_swi1-ix1-2-lan1]: Inter-POP Trunk Core - IXDUB1 - swi1-ix1-1 swi1-ix1-2 - LAN1

# swi1-ix1-1 - swi1-ix2-1
Target[core-swi1-ix1-1_swi1-ix2-1-lan1]: #1\:41:<?php echo $snmppasswd ?>@swi1-ix1-1.inex.ie:::::2
					+ #1\:42:<?php echo $snmppasswd ?>@swi1-ix1-1.inex.ie:::::2
MaxBytes[core-swi1-ix1-1_swi1-ix2-1-lan1]: 2500000000
Directory[core-swi1-ix1-1_swi1-ix2-1-lan1]: trunks
Title[core-swi1-ix1-1_swi1-ix2-1-lan1]: Inter-POP Trunk Core - IXDUB1 - swi1-ix1-1 swi1-ix2-1 - LAN1


# swi1-deg1-1 - swi1-deg1-2
Target[core-swi1-deg1-1_swi1-deg1-2-lan1]: #1\:31:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:32:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-swi1-deg1-1_swi1-deg1-2-lan1]: 2500000000
Directory[core-swi1-deg1-1_swi1-deg1-2-lan1]: trunks
Title[core-swi1-deg1-1_swi1-deg1-2-lan1]: Inter-POP Trunk Core - DEGKCP - swi1-deg1-1 swi1-deg1-2 - LAN1


# swi1-tcy1-1 - swi1-tcy1-3
Target[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: #1\:31:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
					+ #1\:32:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: 2500000000
Directory[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: trunks
Title[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: Intra-POP Trunk Core - TCYDUB1 - swi1-tcy1-1 swi1-tcy1-3 - LAN1


# swi1-tcy1-1 - swi1-vfw-1
Target[core-swi1-tcy1-1_swi1-vfw-1-lan1]: #1\:29:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
					+ #1\:30:<?php echo $snmppasswd ?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-swi1-tcy1-1_swi1-vfw-1-lan1]: 2500000000
Directory[core-swi1-tcy1-1_swi1-vfw-1-lan1]: trunks
Title[core-swi1-tcy1-1_swi1-vfw-1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy1-1 swi1-vfw-1 - LAN1

# swi1-deg1-1 - swi1-tcy3-1
Target[core-swi1-deg1-1_swi1-tcy3-1-lan1]: #1\:29:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:30:<?php echo $snmppasswd ?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-swi1-deg1-1_swi1-tcy3-1-lan1]: 2500000000
Directory[core-swi1-deg1-1_swi1-tcy3-1-lan1]: trunks
Title[core-swi1-deg1-1_swi1-tcy3-1-lan1]: Inter-POP Trunk Core - Telecity NWBP - swi1-deg1-1 swi1-tcy3-1 - LAN1

# swi1-tcy3-1 - swi1-vfw-1
Target[core-swi1-tcy3-1_swi1-vfw-1-lan1]: #1\:41:<?php echo $snmppasswd ?>@swi1-tcy3-1.inex.ie:::::2
					+ #1\:42:<?php echo $snmppasswd ?>@swi1-tcy3-1.inex.ie:::::2
MaxBytes[core-swi1-tcy3-1_swi1-vfw-1-lan1]: 2500000000
Directory[core-swi1-tcy3-1_swi1-vfw-1-lan1]: trunks
Title[core-swi1-tcy3-1_swi1-vfw-1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy3-1 swi1-vfw-1 - LAN1


################
#    LAN2      #
################

# degkcp-tcydub1 - LAN2
Target[core-degkcp-tcydub1-lan2]: #ethernet21:<?php echo $snmppasswd ?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan2]: 1250000000
Directory[core-degkcp-tcydub1-lan2]: trunks
Title[core-degkcp-tcydub1-lan2]: Trunk Core - DEGKCP-TCYDUB1 - LAN2

# degkcp-ixdub1 - LAN2
Target[core-degkcp-ixdub1-lan2]: #ethernet23:<?php echo $snmppasswd ?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-ixdub1-lan2]: 1250000000
Directory[core-degkcp-ixdub1-lan2]: trunks
Title[core-degkcp-ixdub1-lan2]: Trunk Core - DEGKCP-IXDUB1 - LAN2

# tcydub1-ixdub1 - LAN2
Target[core-tcydub1-ixdub1-lan2]: #ethernet21:<?php echo $snmppasswd ?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-tcydub1-ixdub1-lan2]: 1250000000
Directory[core-tcydub1-ixdub1-lan2]: trunks
Title[core-tcydub1-ixdub1-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2
                                              
# degkcp-tcy3 - LAN2
Target[core-degkcp-tcy3-lan2]: #ethernet15:<?php echo $snmppasswd ?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-tcy3-lan2]: 1250000000
Directory[core-degkcp-tcy3-lan2]: trunks
Title[core-degkcp-tcy3-lan2]: Trunk Core - DEGKCP-TCY3 - LAN2

# tcydub1-vfw - LAN2
Target[core-tcydub1-vfw-lan2]: #ethernet15:<?php echo $snmppasswd ?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-tcydub1-vfw-lan2]: 1250000000
Directory[core-tcydub1-vfw-lan2]: trunks
Title[core-tcydub1-vfw-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2

# tcy3-vfw - LAN2
Target[core-tcydub3-vfw-lan2]: #ethernet16:<?php echo $snmppasswd ?>@swi2-tcy3-1.inex.ie:::::2
MaxBytes[core-tcydub3-vfw-lan2]: 1250000000
Directory[core-tcydub3-vfw-lan2]: trunks
Title[core-tcydub3-vfw-lan2]: Trunk Core - TCYDUB3-VFW - LAN2

# ix1 - ix2 - LAN2
Target[core-ix1-ix2-lan2]: #ethernet19:<?php echo $snmppasswd ?>@swi2-ix1-1.inex.ie:::::2
			+ #ethernet20:<?php echo $snmppasswd ?>@swi2-ix1-1.inex.ie:::::2
MaxBytes[core-ix1-ix2-lan2]: 2500000000
Directory[core-ix1-ix2-lan2]: trunks
Title[core-ix1-ix2-lan2]: Trunk Core - Interxion DUB1 to DUB2 - LAN2


# swi2-deg1-2 - swi2-deg1-4
Target[core-swi2-deg1-2_swi2-deg1-4-lan2]: #ethernet19:<?php echo $snmppasswd ?>@swi2-deg1-2.inex.ie:::::2
					+ #ethernet20:<?php echo $snmppasswd ?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-swi2-deg1-2_swi2-deg1-4-lan2]: 2500000000
Directory[core-swi2-deg1-2_swi2-deg1-4-lan2]: trunks
Title[core-swi2-deg1-2_swi2-deg1-4-lan2]: Inter-POP Trunk Core - DEGKCP - swi2-deg1-2 swi2-deg1-4 - LAN2


# swi2-tcy1-2 - swi2-tcy1-4
Target[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: #ethernet19:<?php echo $snmppasswd ?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: 1250000000
Directory[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: trunks
Title[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: Inter-POP Trunk Core - TCYDUB1 - swi2-tcy1-2 swi2-tcy1-4 - LAN2
