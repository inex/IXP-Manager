<?php

// IXP Manager v3 version of graph boxes to fit with the existing UI

?>

<table cellspacing="1" cellpadding="1">
    <tr>
        <td colspan="8">
            <img width="100%" border="0" src="data:image/png;base64,<?=base64_encode( $t->graph->png() )?>" />
        </td>
    </tr>
    <tr>
        <td width="10%">
        </td>
        <td width="25%" align="right">
            <strong>Max&nbsp;&nbsp;&nbsp;&nbsp;</strong>
        </td>
        <td width="25%" align="right">
            <strong>Average&nbsp;&nbsp;&nbsp;&nbsp;</strong>
        </td>
        <td width="25%" align="right">
            <strong>Current&nbsp;&nbsp;&nbsp;&nbsp;</strong>
        </td>
        <td width="15%"></td>
    </tr>
    <tr>
        <td style="color: #00cc00; font-weight: bold;"  align="left">
            In
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->maxIn(), $t->graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->averageIn(), $t->graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->curIn(), $t->graph->category() )?>
        </td>
        <td></td>
    </tr>
    <tr>
        <td style="color: #0000ff; font-weight: bold;"  align="left">
            Out
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->maxOut(), $t->graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->averageOut(), $t->graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $t->graph->statistics()->curOut(), $t->graph->category() )?>
        </td>
        <td></td>
    </tr>
</table>
