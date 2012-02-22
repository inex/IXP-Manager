{include file="header-base.tpl"}


<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="{if $hasIdentity and $identity.user.privs eq 3}container-fluid{else}container{/if}">
            <a class="brand" href="{genUrl}">IXP Manager</a>
            {if $hasIdentity}
                <div class="nav-collapse">
                     <ul class="nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#contact">Contact</a></li>
                     </ul>
                </div><!--/.nav-collapse -->
            {/if}
        </div>
    </div>
</div>
    
<div class="{if $hasIdentity and $identity.user.privs eq 3}container-fluid{else}container{/if}">

