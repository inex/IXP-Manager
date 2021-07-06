<?php

// IXP Manager v3 version of graph boxes to fit with the existing UI

// RRD graphs already have all the information embedded:

if( config('grapher.backends.mrtg.dbtype') === 'rrd' || $t->graph->classType() === "Smokeping" ): ?>

    <img width="100%" border="0" src="data:image/png;base64,<?=base64_encode( $t->graph->png() )?>" />

<?php else: ?>

    <table cellspacing="1" cellpadding="1" style="font-size: 12px;">
        <tr>
            <td colspan="8">
                <img class="img-fluid" src="data:image/png;base64,<?=base64_encode( $t->graph->png() )?>" />
            </td>
        </tr>
        <tr>
            <td width="10%">
            </td>
            <td width="25%" class="text-right">
                <b>Max&nbsp;&nbsp;&nbsp;&nbsp;</b>
            </td>
            <td width="25%" class="text-right">
                <b>Average&nbsp;&nbsp;&nbsp;&nbsp;</b>
            </td>
            <td width="25%" class="text-right">
                <b>Current&nbsp;&nbsp;&nbsp;&nbsp;</b>
            </td>
            <td width="15%"></td>
        </tr>
        <tr>
            <td style="color: #00cc00;"  class="text-left">
                <b>
                    In
                </b>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->maxIn(), $t->graph->category() )?>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->averageIn(), $t->graph->category() )?>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->curIn(), $t->graph->category() )?>
            </td>
            <td></td>
        </tr>
        <tr>
            <td style="color: #0000ff;"  class="text-left">
                <b>
                    Out
                </b>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->maxOut(), $t->graph->category() )?>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->averageOut(), $t->graph->category() )?>
            </td>
            <td class="text-right">
                <?=$this->grapher()->scale( $t->graph->statistics()->curOut(), $t->graph->category() )?>
            </td>
            <td></td>
        </tr>
    </table>

<?php endif; ?>