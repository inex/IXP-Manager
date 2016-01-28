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
### AGGREGATE GRAPHS
###
### Source:    <?php echo $this->path() . "\n"; ?>
###
#####################################################################################################################
#####################################################################################################################
#####################################################################################################################

<?php
    $ixpMaxBytes = 0;
    foreach( $portsByInfrastructure as $infraid => $infra ):

        $ixpMaxBytes += $infra['maxbytes'];

        foreach( \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES as $ttype => $trafficType ):

            $mrtglabel = "ixp_peering-{$infra['aggregate_graph_name']}-{$ttype}"; ?>

#####################################################################################################################
#
# <?php echo "{$infra['name']} {$trafficType['name']} traffic\n"; ?>
#

Target[<?=$mrtglabel?>]:   <?=implode(' + ',$infra['mrtgIds'][$ttype])."\n"?>
MaxBytes[<?=$mrtglabel?>]: <?php if( $ttype == 'bits' ){echo $infra['maxbytes']."\n";} else {echo round( $infra['maxbytes'] / 64 )."\n";} ?>
Title[<?=$mrtglabel?>]:    <?=config('identity.orgname') . " {$trafficType['name']} / second on {$infra['name']}\n"?>
Options[<?=$mrtglabel?>]:  <?=$trafficType['options']."\n"?>
YLegend[<?=$mrtglabel?>]:  <?=$trafficType['name']?> / Second

    <?php
        endforeach; // foreach( \Utils\Grapher\Mrtg::TRAFFIC_TYPES as $ttype => $trafficType )
    endforeach; // foreach( $portsByInfrastructure as $infraid => $infra ):
?>

<?php
    $ixpAggregateGraphName = sprintf( "ixp%03d", $ixp->getId() );

    foreach( \IXP\Utils\Grapher\Mrtg::TRAFFIC_TYPES as $ttype => $trafficType ):

        $mrtglabel = "ixp_peering-{$ixpAggregateGraphName}-{$ttype}"; ?>


#####################################################################################################################
#
# Aggregate <?=$trafficType['name']?> on entire exchange (IXP ID <?=$ixp->getId()?>)
#

Target[<?=$mrtglabel?>]:   <?php $i=0; foreach( $portsByInfrastructure as $infra ){ $i++; echo implode(' + ',$infra['mrtgIds'][$ttype]); if( $i != count( $portsByInfrastructure ) ) { echo ' + '; } } echo "\n"; ?>
MaxBytes[<?=$mrtglabel?>]: <?php if( $ttype == 'bits' ){ echo $ixpMaxBytes; } else { echo round( $ixpMaxBytes / 64 ); } echo "\n"; ?>
Title[<?=$mrtglabel?>]:    <?=config('identity.orgname') . " Aggregate Traffic - {$trafficType['name']} / second\n"?>
Options[<?=$mrtglabel?>]:  <?=$trafficType['options']."\n"?>
YLegend[<?=$mrtglabel?>]:  <?=$trafficType['name']?> / Second

<?php
    endforeach; // foreach( \Utils\Grapher\Mrtg::TRAFFIC_TYPES as $ttype => $trafficType ):
?>

#####################################################################################################################
#####################################################################################################################
#####################################################################################################################
