{tmplinclude file="header.tpl"}

{assign var='_inc_file' value=$controller|cat:'/addEdit-preamble.tpl'}
{include_if_exists file=$_inc_file}

<div class="content">

<h2>IP Addresses :: Add New </h2>

{$form}

</div>

{assign var='_inc_file' value=$controller|cat:'/addEdit-postamble.tpl'}
{include_if_exists file=$_inc_file}

{tmplinclude file="footer.tpl"}
