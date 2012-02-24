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

<ul>

{foreach from=$custs item=cust}

	<li>
		<a href="{genUrl controller="dashboard" action="statistics" shortname=$cust.shortname}">
			{$cust.name}
		</a>
	</li>

{/foreach}

</ul>

{include file="footer.tpl"}