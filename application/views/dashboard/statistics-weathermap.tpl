{include file="header.tpl" mode='fluid'}

<h2>{$weathermap.name}</h2>

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
