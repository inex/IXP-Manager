<?php

// IXP Manager v3 version of graph boxes to fit with the existing UI

?>

<table width="506" cellspacing="1" cellpadding="1">
    <tr>
        <td colspan="8" style="width: 500; height: 135;">
            <img width="500" height="135" border="0" src="" />
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
            <?=$this->grapher()->scale( $graph->statistics()->maxIn(), $graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $graph->statistics()->averageIn(), $graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $graph->statistics()->curIn(), $graph->category() )?>
        </td>
        <td></td>
    </tr>
    <tr>
        <td style="color: #0000ff; font-weight: bold;"  align="left">
            Out
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $graph->statistics()->maxOut(), $graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $graph->statistics()->averageOut(), $graph->category() )?>
        </td>
        <td align="right">
            <?=$this->grapher()->scale( $graph->statistics()->curOut(), $graph->category() )?>
        </td>
        <td></td>
    </tr>
</table>
