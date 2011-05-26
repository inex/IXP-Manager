{include file="header.tpl" pageTitle="IXP Manager :: Drupal Login"}

<div class="yui-g">

<table class="adminheading" border="0">
<tr>
    <th class="Drupal">
        Drupal Login
    </th>
</tr>
</table>

{include file="message.tpl"}

<div id='ajaxMessage'></div>


<div class="login">
    <div class="login-form">
        <h1>Drupal Login</h1>
        <form action="https://www.inex.ie/user" method="post" name="loginForm" id="loginForm">
        <div class="form-block">
            <div class="inputlabel">Username</div>

            <div><input name="name" autocomplete="off" type="text" class="inputbox" size="15" value="{$user->username}" readonly /></div>
            <div class="inputlabel">Password</div>
            <div><input name="pass" autocomplete="off" type="password" class="inputbox" size="15" /></div>
            <div align="left">
                <input type="hidden" name="form_id" id="edit-user-login" value="user_login" />
                <input type="submit" name="op" class="button" value="Log in" />
            </div>
        </div>
        </form>
    </div>
    <div class="login-text">

        <div class="ctr"><img src="{genUrl}/images/joomla-admin/security.png" width="64" height="64" alt="security" /></div>
        <p>Use this form to login to the INEX's Drupal content management system.</p>
        <p>For security reasons, we require you to re-enter your password.</p>
    </div>
    <div class="clr"></div>
</div>

</div>


{include file="footer.tpl"}
