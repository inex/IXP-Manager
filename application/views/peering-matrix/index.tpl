{if isset( $hasIdentity ) and $hasIdentity}
    {include file="header.tpl" mode="fluid"}
{else}
    {include file="header.tpl" mode="fluid" brand="INEX - Internet Neutral Exchange - Peering Matrix"}
{/if}

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
            <div class="btn-group">
                <button id="btn-zoom-out" class="btn btn-mini"><i class="icon-zoom-out"></i></button>
                <button id="btn-zoom-in"  class="btn btn-mini"><i class="icon-zoom-in"></i></button>
            </div>
        </div>
    </li>
</ul>

<div class="row-fluid">



<table id="table-pm" class="pm-table">

<colgroup id="cg-name"></colgroup>
<colgroup id="cg-asn"></colgroup>
{foreach from=$custs key=x_as item=peers}
    <colgroup id="cg-{$x_as}"></colgroup>
{/foreach}

<thead>

    <tr>
    
        <th id="th-name" class="name zoom3"></th>
        <th id="th-asn" class="asn zoom3"></th>
    
        {assign var=cnt value=0}
        {foreach from=$custs key=x_as item=peers}
    
            <th id="th-{$cnt}" class="zoom3">
                {assign var=asn value=$x_as|string_format:$asnStringFormat}
                {assign var=len value=strlen( $asn )}
                {for $pos=0 to $len}
                    {$asn|truncate:1:''}{if $pos < $len}<br />{/if}
                    {assign var=asn value=substr( $asn, 1 )}
                {/for}
            </th>
    
            {assign var=cnt value=$cnt+1}
    
        {/foreach}
    
    </tr>

</thead>

<tbody>

{assign var=outer value=0}

{foreach from=$custs key=x_as item=x}


	<tr id="tr-name-{$x_as}">

	    <td id="td-name-{$x_as}" class="name zoom3">{$x.name}</td>
	    <td id="td-asn-{$x_as}" class="asn zoom3">{$x.autsys}</td>

        {assign var=inner value=0}

	    {foreach from=$custs key=y_as item=y}

		    <td id="td-{$x_as}-{$y_as}" class="
		        {if $y.autsys eq $x.autsys}
		        {else if isset( $sessions.$x_as.peers.$y_as )}
		            peered
		        {else if $x.rsclient and $y.rsclient}
		            peered
	            {else}
		            notpeered
		        {/if}
		         zoom3">
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

{if isset( $user.privs ) and $user.privs eq 3}
</div>
{/if}

<script type="text/javascript">
{include file="peering-matrix/index.js"}
</script>

{include file="footer.tpl"}

