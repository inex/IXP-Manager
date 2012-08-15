{include file="header.tpl"}
<div class="page-content">

    <div class="page-header">
        <h1>User Admin for {$customer.name}</h1>
    </div>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

{if $isEdit}
    <h3>Edit User</h3>

    <p>
    Please edit your users email address and mobile number below.
    </p>

{else}

    <h3>Add New User</h3>

    <p>
    Please complete the form below to add a new user to your account.
    </p>
    
    <p>
    Your new user's account will be locked until they set their password via the forgotten password
    procedure (available on the login page and instructions included in the welcome email which
    will be sent on completion of the below form).
    </p>

{/if}

<br />
<br />
{$form}


{include file="footer.tpl"}

