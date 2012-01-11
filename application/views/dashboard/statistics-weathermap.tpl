{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        {$weathermap.name}
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>



<iframe src="{$weathermap.url}" 
		frameborder="0" 
		scrolling="no" 
		width="{$weathermap.width}" 
		height="{$weathermap.height}"
		style="margin: 0; padding: 0; margin-left: auto; margin-right: auto;"
	></iframe>


</div>
</div>

{tmplinclude file="footer.tpl"}

