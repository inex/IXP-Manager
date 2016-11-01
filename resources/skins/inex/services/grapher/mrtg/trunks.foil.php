<?php /*
    MRTG Configuration Templates

    INEX Switch Trunk Graphs - a production example.
    
    Note that we created an additional config file called config/custom.php where you can
    place your own customised config options. As all our production switches use the same
    SNMP password, we set it there and use it below.

    These graph definitions match up with config/grapher_trunks.php

*/ ?>


################
#    LAN1      #
################

# degkcp-tcydub1 - LAN1 - Primary
Target[core-degkcp-tcydub1-lan1]: #1\:33:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:34:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:35:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2 
				+ #1\:36:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:37:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
 	 			+ #1\:38:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:39:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:40:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan1]: 9000000000
Directory[core-degkcp-tcydub1-lan1]: trunks
Title[core-degkcp-tcydub1-lan1]: Trunk Core - DEGKCP-TCYDUB1 - LAN1 - Primary

# degkcp-tcydub1 - LAN1 - Secondary
Target[core-degkcp-tcydub1-lan1-sec]: #1\:41:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:42:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:43:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:44:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:45:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:46:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:47:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:48:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan1-sec]: 9000000000
Directory[core-degkcp-tcydub1-lan1-sec]: trunks
Title[core-degkcp-tcydub1-lan1-sec]: Trunk Core - DEGKCP-TCYDUB1 - LAN1 - Secondary

# tcydub1-tcydub1 - LAN1 - Primary
Target[core-tcydub1-tcydub1-lan1]: #1\:49:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:53:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-tcydub1-tcydub1-lan1]: 9000000000
Directory[core-tcydub1-tcydub1-lan1]: trunks
Title[core-tcydub1-tcydub1-lan1]: Trunk Core - TCYDUB1 Internal - LAN1 - Primary


# degkcp-ixdub1 - LAN1
Target[core-degkcp-ixdub1-lan1]: #1\:25:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:26:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
				+ #1\:27:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-degkcp-ixdub1-lan1]: 3750000000
Directory[core-degkcp-ixdub1-lan1]: trunks
Title[core-degkcp-ixdub1-lan1]: Trunk Core - TCYKCP-IXDUB1 - LAN1

# tcydub1-ixdub1 - LAN1
Target[core-tcydub1-ixdub1-lan1]: #1\:25:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:26:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
				+ #1\:27:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-tcydub1-ixdub1-lan1]: 3750000000
Directory[core-tcydub1-ixdub1-lan1]: trunks
Title[core-tcydub1-ixdub1-lan1]: Trunk Core - TCYDUB1-IXDUB1 - LAN1

# swi1-ix1-1 - swi1-ix1-2
Target[core-swi1-ix1-1_swi1-ix1-2-lan1]: #1\:45:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-ix1-1.inex.ie:::::2
					 + #1\:46:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-ix1-1.inex.ie:::::2
MaxBytes[core-swi1-ix1-1_swi1-ix1-2-lan1]: 2500000000
Directory[core-swi1-ix1-1_swi1-ix1-2-lan1]: trunks
Title[core-swi1-ix1-1_swi1-ix1-2-lan1]: Inter-POP Trunk Core - IXDUB1 - swi1-ix1-1 swi1-ix1-2 - LAN1

# swi1-ix1-1 - swi1-ix2-1
Target[core-swi1-ix1-1_swi1-ix2-1-lan1]: #1\:41:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-ix1-1.inex.ie:::::2
					+ #1\:42:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-ix1-1.inex.ie:::::2
MaxBytes[core-swi1-ix1-1_swi1-ix2-1-lan1]: 2500000000
Directory[core-swi1-ix1-1_swi1-ix2-1-lan1]: trunks
Title[core-swi1-ix1-1_swi1-ix2-1-lan1]: Inter-POP Trunk Core - IXDUB1 - swi1-ix1-1 swi1-ix2-1 - LAN1


# swi1-deg1-1 - swi1-deg1-3
Target[core-swi1-deg1-1_swi1-deg1-3-lan1]: #1\:31:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:32:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-swi1-deg1-1_swi1-deg1-3-lan1]: 2500000000
Directory[core-swi1-deg1-1_swi1-deg1-3-lan1]: trunks
Title[core-swi1-deg1-1_swi1-deg1-3-lan1]: Inter-POP Trunk Core - DEGKCP - swi1-deg1-1 swi1-deg1-3 - LAN1


# swi1-tcy1-1 - swi1-tcy1-3
Target[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: #1\:31:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
					+ #1\:32:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: 2500000000
Directory[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: trunks
Title[core-swi1-tcy1-1_swi1-tcy1-3-lan1]: Intra-POP Trunk Core - TCYDUB1 - swi1-tcy1-1 swi1-tcy1-3 - LAN1


# swi1-tcy1-1 - swi1-vfw-1
Target[core-swi1-tcy1-1_swi1-vfw-1-lan1]: #1\:29:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
					+ #1\:30:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy1-1.inex.ie:::::2
MaxBytes[core-swi1-tcy1-1_swi1-vfw-1-lan1]: 2500000000
Directory[core-swi1-tcy1-1_swi1-vfw-1-lan1]: trunks
Title[core-swi1-tcy1-1_swi1-vfw-1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy1-1 swi1-vfw-1 - LAN1

# swi1-deg1-1 - swi1-tcy3-1
Target[core-swi1-deg1-1_swi1-tcy3-1-lan1]: #1\:29:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:30:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:21:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
					+ #1\:22:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-deg1-1.inex.ie:::::2
MaxBytes[core-swi1-deg1-1_swi1-tcy3-1-lan1]: 5000000000
Directory[core-swi1-deg1-1_swi1-tcy3-1-lan1]: trunks
Title[core-swi1-deg1-1_swi1-tcy3-1-lan1]: Inter-POP Trunk Core - Telecity NWBP - swi1-deg1-1 swi1-tcy3-1 - LAN1

# swi1-tcy3-1 - swi1-vfw-1
Target[core-swi1-tcy3-1_swi1-vfw-1-lan1]: #1\:41:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy3-1.inex.ie:::::2
					+ #1\:42:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi1-tcy3-1.inex.ie:::::2
MaxBytes[core-swi1-tcy3-1_swi1-vfw-1-lan1]: 2500000000
Directory[core-swi1-tcy3-1_swi1-vfw-1-lan1]: trunks
Title[core-swi1-tcy3-1_swi1-vfw-1-lan1]: Inter-POP Trunk Core - Vodafone - swi1-tcy3-1 swi1-vfw-1 - LAN1


################
#    LAN2      #
################

# degkcp-tcydub1 - LAN2
Target[core-degkcp-tcydub1-lan2]: #ethernet21:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-tcydub1-lan2]: 1250000000
Directory[core-degkcp-tcydub1-lan2]: trunks
Title[core-degkcp-tcydub1-lan2]: Trunk Core - DEGKCP-TCYDUB1 - LAN2

# degkcp-ixdub1 - LAN2
Target[core-degkcp-ixdub1-lan2]: #ethernet23:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-ixdub1-lan2]: 1250000000
Directory[core-degkcp-ixdub1-lan2]: trunks
Title[core-degkcp-ixdub1-lan2]: Trunk Core - DEGKCP-IXDUB1 - LAN2

# tcydub1-ixdub1 - LAN2
Target[core-tcydub1-ixdub1-lan2]: #ethernet21:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-tcydub1-ixdub1-lan2]: 1250000000
Directory[core-tcydub1-ixdub1-lan2]: trunks
Title[core-tcydub1-ixdub1-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2
                                              
# degkcp-tcy3 - LAN2
Target[core-degkcp-tcy3-lan2]: #ethernet15:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-degkcp-tcy3-lan2]: 1250000000
Directory[core-degkcp-tcy3-lan2]: trunks
Title[core-degkcp-tcy3-lan2]: Trunk Core - DEGKCP-TCY3 - LAN2

# tcydub1-vfw - LAN2
Target[core-tcydub1-vfw-lan2]: #ethernet15:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-tcydub1-vfw-lan2]: 1250000000
Directory[core-tcydub1-vfw-lan2]: trunks
Title[core-tcydub1-vfw-lan2]: Trunk Core - TCYDUB1-IXDUB1 - LAN2

# tcy3-vfw - LAN2
Target[core-tcydub3-vfw-lan2]: #ethernet16:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-tcy3-1.inex.ie:::::2
MaxBytes[core-tcydub3-vfw-lan2]: 1250000000
Directory[core-tcydub3-vfw-lan2]: trunks
Title[core-tcydub3-vfw-lan2]: Trunk Core - TCYDUB3-VFW - LAN2

# ix1 - ix2 - LAN2
Target[core-ix1-ix2-lan2]: #ethernet19:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-ix1-1.inex.ie:::::2
			+ #ethernet20:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-ix1-1.inex.ie:::::2
MaxBytes[core-ix1-ix2-lan2]: 2500000000
Directory[core-ix1-ix2-lan2]: trunks
Title[core-ix1-ix2-lan2]: Trunk Core - Interxion DUB1 to DUB2 - LAN2


# swi2-deg1-2 - swi2-deg1-4
Target[core-swi2-deg1-2_swi2-deg1-4-lan2]: #ethernet19:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-deg1-2.inex.ie:::::2
					+ #ethernet20:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-deg1-2.inex.ie:::::2
MaxBytes[core-swi2-deg1-2_swi2-deg1-4-lan2]: 2500000000
Directory[core-swi2-deg1-2_swi2-deg1-4-lan2]: trunks
Title[core-swi2-deg1-2_swi2-deg1-4-lan2]: Inter-POP Trunk Core - DEGKCP - swi2-deg1-2 swi2-deg1-4 - LAN2


# swi2-tcy1-2 - swi2-tcy1-4
Target[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: #ethernet19:<?=config('custom.grapher.snmp_password','xxxxxx')?>@swi2-tcy1-2.inex.ie:::::2
MaxBytes[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: 1250000000
Directory[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: trunks
Title[core-swi2-tcy1-2_swi2-tcy1-4-lan2]: Inter-POP Trunk Core - TCYDUB1 - swi2-tcy1-2 swi2-tcy1-4 - LAN2
