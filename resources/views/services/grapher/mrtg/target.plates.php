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

            if( $cnt > 1 && $cnt % 2 == 0 && $cnt < $numports ):
                echo "\n        + ";
            elseif( $cnt > 0 && $cnt < $numports ):
                    echo ' + ';
            endif;

            echo "{$trafficType['in']}#" . $data['pis'][$piid]->getSwitchPort()->ifnameToSNMPIdentifier() . "&{$trafficType['out']}#"
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
