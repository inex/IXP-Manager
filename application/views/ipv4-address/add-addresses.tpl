{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        IP Addressing
    </li>
    <li class="active">
        <a href="{genUrl controller='ipv4-address' action='add-addresses'}">Add Addresses</a>
    </li>
</ul>

{include file="message.tpl"}

<h4>Please note:</h4>

<ul>
	<li> IPv6 addresses assume that the first address number is given in hex. IPv4 assume decimal. </li>
	<li> The number of addresses to create is always expressed in decimal. </li>
	<li> Clicking generate will show you the addresses that will be created. A further action is required to commit. </li>
</ul>

{$form}


{include file="footer.tpl"}
