<?php


/*
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * Generate a URL for an Mrtg image.
 *
 */
function smarty_function_genMrtgGraphBox( $params, &$smarty )
{
    $url = IXP_Mrtg::generateZendFrontendUrl( $params );

    $box = <<<END_BOX
<table width="506" cellspacing="1" cellpadding="1">
<tr>
    <td colspan="8" style="width: 500; height: 135;">
        <img width="500" height="135" border="0" src="{$url}" />
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
        {$params['values']['maxin']}
    </td>
    <td align="right">
        {$params['values']['averagein']}
    </td>
    <td align="right">
        {$params['values']['curin']}
    </td>
    <td></td>
</tr>
<tr>
    <td style="color: #0000ff; font-weight: bold;"  align="left">
        Out
    </td>
    <td align="right">
        {$params['values']['maxout']}
    </td>
    <td align="right">
        {$params['values']['averageout']}
    </td>
    <td align="right">
        {$params['values']['curout']}
    </td>
    <td></td>
</tr>
</table>
END_BOX;

    return $box;
}

