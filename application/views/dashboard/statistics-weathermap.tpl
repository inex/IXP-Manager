{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li class="active">
            {$weathermap.name}
        </li>
    </ul>
{else}
    <div class="page-content">
    
        <div class="page-header">
            <h1>{$weathermap.name}</h1>
        </div>
{/if}

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<iframe src="{$weathermap.url}"
		frameborder="0"
		scrolling="no"
		width="100%"
		height="{$weathermap.height}"
		style="margin: 0; padding: 0; margin-left: auto; margin-right: auto;"
	></iframe>

{include file="footer.tpl"}
