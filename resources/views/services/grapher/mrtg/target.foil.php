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

            if( $numports > 1 && $cnt === 0 ):
                echo "\n          ";
            elseif( $cnt > 0 && $cnt < $numports ):
                echo " + \n          ";
            endif;

            echo
                substr( $this->data['pis'][$piid]->switchPort->{$trafficType['in']}(), 1 ) . "#"
                    . $this->data['pis'][$piid]->switchPort->ifnameToSNMPIdentifier()
                    . "&" . substr( $this->data['pis'][$piid]->switchPort->{$trafficType['out']}(), 1 ) . "#"
                    . $this->data['pis'][$piid]->switchPort->ifnameToSNMPIdentifier()
                    . ":" .  $t->grapher()->escapeCommunityForMrtg( $this->data['pis'][$piid]->switchPort->switcher->snmppasswd )
                    . "@" . $this->data['pis'][$piid]->switchPort->switcher->hostname . ":::::2";

            $cnt++;
        endforeach; ?>

Title[<?=$mrtglabel?>]:     <?=sprintf( "{$this->graphTitle}\n", $trafficType['name'] )?>
Options[<?=$mrtglabel?>]:   <?= $trafficType['options'] . ( strlen( $trafficType['options'] ) ? ' , ' : ' ' ) . "pngdate\n"?>
YLegend[<?=$mrtglabel?>]:   <?=$trafficType['name']?> / Second
MaxBytes[<?=$mrtglabel?>]:  <?= ( $ttype === IXP\Services\Grapher\Graph::CATEGORY_BITS ? $t->maxbytes : round( $t->maxbytes / 64 ) ) . "\n" ?>
<?= $t->ifnot( 'directory', '!E' ) !== '!E' ? "Directory[{$mrtglabel}]: {$t->directory}\n" : "\n" ?>

<?php
    endforeach;
?>
