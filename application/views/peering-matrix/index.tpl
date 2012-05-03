
{include file="header.tpl" mode="fluid" brand="INEX - Internet Neutral Exchange - Peering Matrix"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl}">Peering Matrices</a>
    </li>
    <li class="active">
        for the {$lans.$lan} using {$protos.$proto}
    </li>

    <li class="pull-right">
        <div class="btn-toolbar" style="display: inline;">
            <div class="btn-group">
                <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
                    {$lans.$lan}
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    {foreach from=$lans key=id item=name}
                        <li> <a href="{genUrl controller="peering-matrix" action="index" lan=$id proto=$proto}">{$name}</a> </li>
                    {/foreach}
                </ul>
            </div>
            <div class="btn-group">
                <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
                    {$protos.$proto}
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    {foreach from=$protos key=id item=name}
                        <li> <a href="{genUrl controller="peering-matrix" action="index" lan=$lan proto=$id}">{$name}</a> </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </li>
</ul>

<div class="row-fluid">



<table class="pm-table">

<thead>

    <tr>
    
        <th class="name"></th>
        <th class="asn"></th>
    
        {foreach from=$custs key=x_as item=peers}
    
            <th>
                {assign var=asn value=$x_as|string_format:$asnStringFormat}
                {assign var=len value=strlen( $asn )}
                {for $pos=0 to $len}
                    {$asn|truncate:1:''}{if $pos < $len}<br />{/if}
                    {assign var=asn value=substr( $asn, 1 )}
                {/for}
            </th>
    
        {/foreach}
    
    </tr>

</thead>

<tbody>

{assign var=outer value=0}

{foreach from=$custs key=x_as item=x}


	<tr>

	    <td class="name">{$x.name}</td>
	    <td class="asn">{$x.autsys}</td>

        {assign var=inner value=0}

	    {foreach from=$custs key=y_as item=y}

		    <td class="
		        {if $y.autsys eq $x.autsys}
		        {else if isset( $sessions.$x_as.peers.$y_as )}
		            peered
		        {else if $x.rsclient and $y.rsclient}
		            peered
	            {else}
		            notpeered
		        {/if}
		        ">
		    </td>

        {assign var=inner value=$inner+1}

        {* for the last cell of the last row, we add a empty cell *}
        {if $outer eq $custs|@count and $inner eq $custs|@count}
            <td></td>
        {/if}
	    {/foreach}

	</tr>

{assign var=outer value=$outer+1}

{/foreach}

</tbody>

</table>


{include file="footer.tpl"}

