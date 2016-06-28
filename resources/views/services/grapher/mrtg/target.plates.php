<?php
    foreach( $trafficTypes as $ttype => $trafficType ):

        $mrtglabel = sprintf( "{$mrtgPrefix}-{$ttype}" ); ?>

#####################################################################################################################
#
# <?="{$mrtglabel}\n"?>
#

Target[<?=$mrtglabel?>]:    <?php
        $cnt = 0;
        $numports = count( $portIds );
        foreach( $portIds as $piid ):

            if( $numports > 1 && $cnt == 0 ):
                echo "\n          ";
            elseif( $cnt > 0 && $cnt < $numports ):
                echo " + \n          ";
            endif;

            echo
                substr( $data['pis'][$piid]->getSwitchPort()->{$trafficType['in']}(), 1 ) . "#"
                    . $data['pis'][$piid]->getSwitchPort()->ifnameToSNMPIdentifier()
                    . "&" . substr( $data['pis'][$piid]->getSwitchPort()->{$trafficType['out']}(), 1 ) . "#"
                    . $data['pis'][$piid]->getSwitchPort()->ifnameToSNMPIdentifier()
                    . ":" .  $data['pis'][$piid]->getSwitchPort()->getSwitcher()->getSnmppasswd()
                    . "@" . $data['pis'][$piid]->getSwitchPort()->getSwitcher()->getHostname() . ":::::2";

            $cnt++;
        endforeach; ?>

Title[<?=$mrtglabel?>]:     <?=sprintf( "{$graphTitle}\n", $trafficType['name'] )?>
Options[<?=$mrtglabel?>]:   <?=$trafficType['options']."\n"?>
YLegend[<?=$mrtglabel?>]:   <?=$trafficType['name']?> / Second
<?=isset( $directory ) ? "Directory[{$mrtglabel}]: {$directory}\n" : ""?>

<?php
    endforeach;
?>
