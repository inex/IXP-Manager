{tmplinclude file="auth/header.tpl"}

{tmplinclude file="message.tpl"}


<div class="login">
    <div class="login-form">
        <h1>Login</h1>
        <form action="{genUrl controller="auth" action="process"}" method="post" name="loginForm" id="loginForm">
        <div class="form-block">
            <div class="inputlabel">Username</div>

            <div><input name="loginusername" autocomplete="off" type="text" class="inputbox" size="15" value="{if isset($username)}{$username}{/if}" /></div>
            <div class="inputlabel">Password</div>
            <div><input name="loginpassword" autocomplete="off" type="password" class="inputbox" size="15" /></div>
            <div align="left">
                <input type="submit" name="submit" class="button" value="Login" />
                <a href="{genUrl controller="auth" action="forgotten-password"}">Forgotten Password?</a>
            </div>
        </div>
        </form>
    </div>
    <div class="login-text">

        <div class="ctr"><img src="{genUrl}/images/joomla-admin/security.png" width="64" height="64" alt="security" /></div>
        <p><strong>Welcome to INEX's IXP Manager!</strong></p>
        <p>For help please contact <br />{mailto address='operations@inex.ie' encode='javascript' note='the operations team'}.</p>
    </div>
    <div class="clr"></div>
</div>


{tmplinclude file="auth/footer.tpl"}
