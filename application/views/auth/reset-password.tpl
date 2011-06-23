{tmplinclude file="auth/header.tpl"}

{tmplinclude file="message.tpl"}


<div class="login">
    <div class="login-form">
        <h1>Reset Password</h1>
        <br /><br />
        <form action="{genUrl controller="auth" action="reset-password"}" method="post" name="loginForm" id="loginForm">
        <div class="form-block">
            <div class="inputlabel">Username</div>
            <div><input name="username" autocomplete="off" type="text" class="inputbox" size="15" value="{if isset($username)}{$username}{/if}" /></div>
            <div class="inputlabel">Token</div>
            <div><input name="token" autocomplete="off" type="text" class="inputbox" size="15" value="{if isset($token)}{$token}{/if}" /></div>
            <div class="inputlabel">New Password</div>
            <div><input name="pass1" autocomplete="off" type="password" class="inputbox" size="15" value="" /></div>
            <div class="inputlabel">Confirm New Password</div>
            <div><input name="pass2" autocomplete="off" type="password" class="inputbox" size="15" value="" /></div>
            <div align="left">
                <input type="hidden" name="fpsubmitted" value="1" />
                <input type="submit" name="submit" class="button" value="Submit" />
                <a href="{genUrl controller="auth"}">Return to Login Page</a>
            </div>
        </div>
        </form>
    </div>
    <div class="login-text">

        <div class="ctr"><img src="images/joomla-admin/security.png" width="64" height="64" alt="security" /></div>
        <p>
        	Please enter your username, the token that was emailed to you and a new
        	password on the right.
        </p>
        <p>
            For help please contact <br />{mailto address='operations@inex.ie' encode='javascript' note='the operations team'}.
        </p>
    </div>
    <div class="clr"></div>
</div>


{tmplinclude file="footer.tpl"}
