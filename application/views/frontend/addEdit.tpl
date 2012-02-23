{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl contoller="customer" action="list"}">Customers</a> <span class="divider">/</span>
    </li>
    <li class="active">
		{if $isEdit}
		    Edit
		{else}
		    Add New 
		{/if}
    </li>
</ul>       


{$form}

{if isset( $hasPostContent ) and $hasPostContent}
    {include file=$hasPostContent}
{/if}

{include file="footer.tpl"}
