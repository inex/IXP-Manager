{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller=$controller action="list"}">{$frontend.pageTitle}</a> <span class="divider">/</span>
    </li>
    <li class="active">
		{if $isEdit}
		    Edit
		{else}
		    Add New
		{/if}
    </li>
</ul>

{if isset( $hasPreContent ) and $hasPreContent}
    {include file=$hasPreContent}
{/if}

{$form}

{if isset( $hasPostContent ) and $hasPostContent}
    {include file=$hasPostContent}
{/if}

{include file="footer.tpl"}
