{include file="header.tpl"}


<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        Statistics <span class="divider">/</span>
    </li>
    <li class="active">
        List
    </li>
</ul>

{include file="message.tpl"}

<div class="row-fluid">

{assign var='count' value=0}

    <div class="span3">

        <ul>
        
        {foreach from=$custs item=cust}

            {if count( $custs ) > 12 and $count >= count( $custs )/4}
            
                </ul>
                </div>
                <div class="span3">
                {assign var='count' value=0}
            {/if}
            
        	<li>
        		<a href="{genUrl controller="dashboard" action="statistics" shortname=$cust.shortname}">
        			{$cust.name}
        		</a>
        	</li>

        	{assign var='count' value=$count+1}
        	
    	{/foreach}

    	</ul>

	</div>
</div>

{include file="footer.tpl"}
