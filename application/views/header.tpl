{include file="header-base.tpl"}


<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="{if $hasIdentity and $identity.user.privs eq 3}container-fluid{else}container{/if}">
            <a class="brand" href="{genUrl}">IXP Manager</a>
            {if $hasIdentity}
                <div class="nav-collapse">
                     <ul class="nav">
                        
                        <li>
                            {if     $user.privs eq 3}<a href="{genUrl}">Home</a>
                            {elseif $user.privs eq 2}<a href="{genUrl controller="cust-admin"}">User Admin</a>
                            {elseif $user.privs eq 1}<a href="{genUrl controller="dashboard"}">Dashboard</a>
                            {/if}
                        </li>
                     </ul>
                </div><!--/.nav-collapse -->
            {/if}
        </div>
    </div>
</div>
    
{if $hasIdentity and $user.privs eq 3}
    
    <div class="container-fluid">

    {include file="menu.tpl"}
    
{else}

    <div class="container">

{/if}
