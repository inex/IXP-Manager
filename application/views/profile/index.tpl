{include file="header.tpl" pageTitle="IXP Manager :: "|cat:$frontend.pageTitle}

<div class="yui-g" style="height: 600px">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="profile">
        User Profile
    </th>
</tr>
</table>

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

</div>
</div>

{include file="footer.tpl"}