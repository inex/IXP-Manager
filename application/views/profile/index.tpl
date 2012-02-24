{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li class="active">
            <a href="{genUrl controller=$controller action=$action}">My Profile</a>
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>User Profile</h1>
        </div>
{/if}

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<p>
This is your INEX user profile where you can change you contact preferences and password.
</p>

<p>
Please note that the mobile number is used to send you password reminders and should contain the country code
in the format: <code>353861234567</code>.
</p>

<div class="row-fluid">
    <div class="span6">

        <h3>Change Your Profile</h3>
        
        {$profileForm}
        
    </div>
    <div class="span6">

        <h3>Change Your Password</h3>
        
        {$passwordForm}
        
    </div>
</div>

{include file="footer.tpl"}
