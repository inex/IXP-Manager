<?php /*
    MRTG Configuration Templates

    Please see: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs

    You should not need to edit these files - instead use your own custom skins. If
    you can't effect the changes you need with skinning, consider posting to the mailing
    list to see if it can be achieved / incorporated.

    Skinning: https://github.com/inex/IXP-Manager/wiki/Skinning */
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
###
###
###
### SWITCH AGGREGATE GRAPHS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    foreach( $portsByInfrastructure as $infraid => $infra ):
        foreach( $infra['switches'] as $switch ):
            foreach( IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES as $ttype => $trafficType ):

                $mrtglabel = "switch-aggregate-{$switch['name']}-{$ttype}"; ?>

#####################################################################################################################
#
# <?=$switch['name']?> <?=$trafficType['name']?> traffic
#


Target[<?=$mrtglabel?>]:   <?=implode(' + ', $switch['mrtgIds'][$ttype])."\n"?>
MaxBytes[<?=$mrtglabel?>]: <?php if( $ttype == 'bits' ){echo $switch['maxbytes']."\n";} else {echo round( $switch['maxbytes'] / 64 )."\n";}?>
Title[<?=$mrtglabel?>]:    <?=config("identity.orgname") . " - Peering {$trafficType['name']} / second on {$switch['name']}\n"?>
Options[<?=$mrtglabel?>]:  <?=$trafficType['options']."\n"?>
YLegend[<?=$mrtglabel?>]:  <?=$trafficType['name']?> / Second
Directory[<?=$mrtglabel?>]: switches

<?php
            endforeach;
        endforeach;
    endforeach;
?>


#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
