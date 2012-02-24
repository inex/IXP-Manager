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
    Your new user's password will be sent by SMS to the mobile
    number provided and they will receive a welcome email to the email address provided.
    </p>

{/if}


{$form}


{include file="footer.tpl"}

