{tmplinclude file="header.tpl"}


<table class="adminheading" border="0">
<tr>
    <th class="{$frontend.name}">
        {$frontend.pageTitle} :: {if $isEdit}Edit{else}Add New{/if}
    </th>
</tr>
</table>

<div class="content">

{$form}

</div>

{tmplinclude file="footer.tpl"}

