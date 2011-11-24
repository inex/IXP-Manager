{tmplinclude file="header.tpl"}

<div class="yui-g">

{assign var='_inc_file' value=$controller|cat:'/addEdit-preamble.tpl'}
{include_if_exists file=$_inc_file}


<div class="content">

{if $isEdit}
    <h2>{$frontend.pageTitle} :: Edit </h2>
{else}
    <h2>{$frontend.pageTitle} :: Add New </h2>
{/if}

{$form}

</div>

{assign var='_inc_file' value=$controller|cat:'/addEdit-postamble.tpl'}
{include_if_exists file=$_inc_file}

</div>

{tmplinclude file="footer.tpl"}

