
<style>
{literal}
.ltbr {
    background-color: #FFFFFF;
    border: 1px solid #797979;
    border-collapse: collapse;
}

.ltbr th {
    background-color:#F2F2F9;
    text-align: left;
    padding: 0;
}

.ltbr td {
    padding: 0;
}

.ltbr_row {
            font-weight:bold;
}

.ltbr_even {
        padding: 0;
        margin: 0;
        border: 0;
            background-color:#F2F2F9;
}

.ltbr_even td {
        padding: 0;
        margin: 1;
        border: 0;
        background-color:#F2F2F9;
}

.ltbr_odd {
        padding: 0;
        margin: 0;
        border: 0;
            background-color:#FFFFFF;
}

.ltbr_odd td {
        padding: 0;
        margin: 1;
        border: 0;
            background-color:#FFFFFF;
}
{/literal}
</style>

<p>
Total potential sessions: {$potential}.
Active peering sessions: {$active}.
{assign var=active value=$active*100}
Percentage active peering sessions: {$active/$potential|string_format:'%d'}%
</p>

<table border="0" cellpadding="0" cellspacing="2" summary="" class="ltbr">

<tr>

    <th class="pmbuilder_heading">&nbsp;</th>
    <th class="pmbuilder_heading">&nbsp;</th>

    {foreach from=$matrix key=x_as item=peers}

        <th class="pmbuilder_heading" align="center" style="text-align: center;">
            {assign var=asn value=$x_as|string_format:'% 6s'}
            {for $pos=0 to strlen( $asn )}
                {$asn|truncate:1:''}<br />
                {assign var=asn value=substr( $asn, 1 )}
            {/for}
        </th>

    {/foreach}

</tr>


{assign var=outer value=0}

{foreach from=$matrix key=x_as item=peers}


	<tr>

	    <td style="text-align: left" >{$peers[0].X_Cust.name}&nbsp;</td>
	    <td style="text-align: right" >&nbsp;{$peers[0].x_as}&nbsp;</td>

        {assign var=inner value=0}

	    {foreach from=$peers item=y}

		    <td width="21" height="21" border="1" style="border: 1px solid black; background-color:
		        {if $outer eq $inner}
		            white
		        {else if $y.peering_status eq 'YES'}
		            lightgreen
		        {else if !isset( $row.peering_status ) || $row.peering_status eq 'NO'}
		            red
		        {/if}
		        ">
		    </td>

        {assign var=inner value=$inner+1}

        {* for the last cell of the last row, we add a empty cell *}
        {if $outer eq $peers|@count and $inner eq $peers|@count}
            <td></td>
        {/if}
	    {/foreach}

	</tr>

{assign var=outer value=$outer+1}

{/foreach}

</table>

