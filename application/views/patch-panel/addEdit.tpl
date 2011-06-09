{tmplinclude file="header.tpl"}

<div class="content">

<table class="adminheading" border="0">
<tr>
    <th class="PatchPanel">
        {if $isEdit}
            <h2>{$frontend.pageTitle} :: Edit </h2>
        {else}
            <h2>{$frontend.pageTitle} :: Add New </h2>
        {/if}
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>



<h2>{if $isEdit}Edit{else}New{/if} Patch Panel Details</h2>

{if $isEdit}
<strong>Note:</strong> If you edit the co-location reference then all of the ports of this
patch panel will also have their co-location reference updated also.
{/if}

{$form}


</div>

{tmplinclude file="footer.tpl"}

