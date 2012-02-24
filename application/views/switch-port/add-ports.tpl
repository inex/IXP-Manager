{include file="header.tpl"}

<ul class="breadcrumb">
    <li>
        <a href="{genUrl}">Home</a> <span class="divider">/</span>
    </li>
    <li>
        <a href="{genUrl controller=$controller action="list"}">{$frontend.pageTitle}</a> <span class="divider">/</span>
    </li>
    <li class="active">
        Add New Ports
    </li>
</ul>

{$form}


{include file="footer.tpl"}
