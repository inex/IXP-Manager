{tmplinclude file="header.tpl"}

<!-- <div class="yui-g" style="height: 600px"> -->

<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members :: Statistics
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div class="content">

<ul>

{foreach from=$custs item=cust}

	<li>
		<a href="{genUrl controller="dashboard" action="statistics" shortname=$cust.shortname}">
			{$cust.name}
		</a>
	</li>

{/foreach}

</ul>

{tmplinclude file="footer.tpl"}