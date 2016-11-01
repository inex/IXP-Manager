<?php
    foreach( $this->trafficTypes as $ttype => $trafficType ):

        $mrtglabel = sprintf( "{$this->mrtgPrefix}-{$ttype}" ); ?>

#####################################################################################################################
#
# <?="{$mrtglabel}\n"?>
#

Target[<?=$mrtglabel?>]:    <?php
        $cnt = 0;
        $numports = count( $this->portIds );
        foreach( $this->portIds as $piid ):

            if( $numports > 1 && $cnt == 0 ):
                echo "\n          ";
            elseif( $cnt > 0 && $cnt < $numports ):
                echo " + \n          ";
            endif;

            echo
                substr( $this->data['pis'][$piid]->getSwitchPort()->{$trafficType['in']}(), 1 ) . "#"
                    . $this->data['pis'][$piid]->getSwitchPort()->ifnameToSNMPIdentifier()
                    . "&" . substr( $this->data['pis'][$piid]->getSwitchPort()->{$trafficType['out']}(), 1 ) . "#"
                    . $this->data['pis'][$piid]->getSwitchPort()->ifnameToSNMPIdentifier()
                    . ":" .  $this->data['pis'][$piid]->getSwitchPort()->getSwitcher()->getSnmppasswd()
                    . "@" . $this->data['pis'][$piid]->getSwitchPort()->getSwitcher()->getHostname() . ":::::2";

            $cnt++;
        endforeach; ?>

Title[<?=$mrtglabel?>]:     <?=sprintf( "{$this->graphTitle}\n", $trafficType['name'] )?>
Options[<?=$mrtglabel?>]:   <?=$trafficType['options']."\n"?>
YLegend[<?=$mrtglabel?>]:   <?=$trafficType['name']?> / Second
MaxBytes[<?=$mrtglabel?>]:  <?= $ttype == IXP\Services\Grapher\Graph::CATEGORY_BITS ? $t->maxbytes : round( $t->maxbytes / 64 ) ?>
<?=isset( $directory ) ? "Directory[{$mrtglabel}]: {$directory}\n" : ""?>


<?php
    endforeach;
?>
