{tmplinclude file="header.tpl"}

<div class="content">

{if $isEdit}
    <h2>Customer :: Editing <em>{$cust->name}</em></h2>
{else}
    <h2>Customer :: Add New</h2>
{/if}

{$form}

</div>

{tmplinclude file="footer.tpl"}

