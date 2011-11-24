{tmplinclude file="header-full-width.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g" style="width: 100%">

<br />

<table class="adminheading" border="0">
<tr>
    <th class="Peering">
        Peering Matrix :: {$config.peering_matrix.public.$lan.name}
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">

<p>
Total potential sessions: {$potential}.
Active peering sessions: {$active}.
{assign var=active value=`$active*100`}
Percentage active peering sessions: {$active/$potential|string_format:'%d'}%
</p>

<table border="0" cellpadding="0" cellspacing="2" summary="" class="ltbr">

<tr>

    <th class="pmbuilder_heading">&nbsp;</th>
    <th class="pmbuilder_heading">&nbsp;</th>

    {foreach from=$matrix key=x_as item=peers}

        <th class="pmbuilder_heading" align="center" style="text-align: center;">
            {assign var=asn value=$x_as|string_format:'% 5s'}
            {php}
                $asn = $this->get_template_vars( 'asn' );
                for( $i = 0; $i < 5; $i++ )
                    echo substr( $asn, $i, 1 ) . '<br />';
            {/php}
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

		    <td >
		        {if $outer eq $inner}
		            {* we're at the intersection of our AS on the x and y graph - stick in an empter cell *}
		            </td><td>
		        {/if}

		        {if $y.peering_status eq 'YES'}
		            <img class="OSSTooltip" alt="Y" width="21" height="21" border="0"
		                  src="http://static.inex.ie/sites/www.inex.ie/themes/inexbluey/images/ticks/yes.gif"
		                  title="X: {$y.X_Cust.name} (AS{$y.x_as}) <br />
		                         Y: {$y.Y_Cust.name} (AS{$y.y_as})"
		            />
		        {else if $row.peering_status eq 'NO'}
		            <img class="OSSTooltip" alt="N" width="21" height="21" border="0"
		                  src="http://static.inex.ie/sites/www.inex.ie/themes/inexbluey/images/ticks/no.gif"
                          title="X: {$y.X_Cust.name} (AS{$y.x_as}) <br />
                                 Y: {$y.Y_Cust.name} (AS{$y.y_as})"
                    />
		        {/if}
		    </td>

        {assign var=inner value=`$inner+1`}

        {* for the last cell of the last row, we add a empty cell *}
        {if $outer eq $peers|@count and $inner eq $peers|@count}
            <td></td>
        {/if}
	    {/foreach}

	</tr>

{assign var=outer value=`$outer+1`}

{/foreach}

</table>

<h3>Notes on peering matrix</h3>

<ul>
    <li>
        Where an INEX member is not listed on this peering matrix, it is because they are
        currently not actively peering at INEX, or because they have opted out of presenting
        their peering information in this database.
    </li>
    <li>
        This peering matrix is based on Netflow traffic accounting data from the INEX peering
        LANs. It is significantly more accurate the the old RIPE IRRDB peering matrix, which
        is still actively maintained by INEX.
    </li>
    <li>
        This peering matrix only detects if there is bidirectional TCP flow between routers at
        INEX. It cannot detect whether there are actually prefixes swapped betwen routers.
    </li>
    <li>
        This peering matrix will indicate that there is active peering between two members,
        even if their peering sessions are misconfigured with incorrect BGP MD5 secrets.
    </li>
    <li>
        The peering matrix does not yet support IPv6.
    </li>
</ul>

</div>

</div>

<script type="text/javascript">
</script>

{tmplinclude file="footer.tpl"}

