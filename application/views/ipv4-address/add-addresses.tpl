{tmplinclude file="header.tpl"}

{assign var='_inc_file' value=$controller|cat:'/addEdit-preamble.tpl'}
{include_if_exists file=$_inc_file}

<div class="content">

<h2>IP Addresses :: Add New </h2>

<strong>Please note:</strong>

<ul>
	<li> IPv6 addresses assume that the first address number is given in hex. IPv4 assume decimal. </li>
	<li> The number of addresses to create is always expressed in decimal. </li>
	<li> Clicking generate will show you the addresses that will be created. A further action is required to commit. </li>
</ul>

{$form}

</div>

{assign var='_inc_file' value=$controller|cat:'/addEdit-postamble.tpl'}
{include_if_exists file=$_inc_file}

{tmplinclude file="footer.tpl"}
