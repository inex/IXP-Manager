{include file="header.tpl"}

<h2>User Profile</h2>

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<p>
This is your INEX user profile where you can change you contact preferences and password.
</p>

<p>
Please note that the mobile number is used to send you password reminders and should contain the country code
in the format: <code>353861234567</code>.

{$profileForm}

{$passwordForm}

{include file="footer.tpl"}
