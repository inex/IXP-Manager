{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl contoller="customer" action="list"}">Customers</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Send Welcome Email
    </li>
</ul>       

{include file="message.tpl"}

{$form}


{include file="footer.tpl"}