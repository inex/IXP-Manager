{tmplinclude file="header.tpl"}

<table class="adminheading" border="0">
<tr>
    <th class="User">
        User Admin for {$customer.name}
    </th>
</tr>
</table>


{tmplinclude file="message.tpl"}

<div id="ajaxMessage"></div>

<div class="content">

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

</div>

{tmplinclude file="footer.tpl"}

