{tmplinclude file="header-full-width.tpl" pageTitle="IXP Manager :: Member Dashboard" header_full_inc_menu=1}

<div class="yui-g" style="width: 100%">

<br />


<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        {$weathermap.name}
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>

<div id="content">



<iframe src="{$weathermap.url}" 
		frameborder="0" 
		scrolling="no" 
		width="100%" 
		height="{$weathermap.height}"
		style="margin: 0; padding: 0; margin-left: auto; margin-right: auto;"
	></iframe>


</div>
</div>

{tmplinclude file="footer.tpl"}

