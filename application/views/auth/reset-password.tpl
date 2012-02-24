{include file="header.tpl"}

<div class="page-content">

    <div class="page-header">
        <h1>Password Reset</h1>
    </div>

{include file="message.tpl"}

<div class="row">

    <div class="span6 offset4">
        <img src="{genUrl}{$config.identity.ixp.biglogo}" />
    </div>
    
</div>

<div class="row">

    <div class="span6 offset3">
    
        <p align="center">
            Please enter your username, the token that was emailed to you and a new password:
        </p>
        
        <form class="form-horizontal" action="{genUrl controller="auth" action="reset-password"}" method="post" name="loginForm" id="loginForm">

        <fieldset class="control-group" id="div-form-username">
            <label for="username" class="control-label">Username</label>
            <div id="div-controls-username" class="controls">
                <input id="email" name="username" type="text" value="{if isset($username)}{$username}{/if}" />
            </div>
        </fieldset>

        <fieldset class="control-group" id="div-form-token">
            <label for="token" class="control-label">Token</label>
            <div id="div-controls-token" class="controls">
                <input id="token" name="token" type="text" value="{if isset($token)}{$token}{/if}" />
            </div>
        </fieldset>

        <fieldset class="control-group" id="div-form-pass1">
            <label for="pass1" class="control-label">Password</label>
            <div id="div-controls-pass1" class="controls">
                <input id="pass1" name="pass1" type="password" value="" />
            </div>
        </fieldset>

        <fieldset class="control-group" id="div-form-pass2">
            <label for="pass2" class="control-label">Confirm Password</label>
            <div id="div-controls-pass2" class="controls">
                <input id="pass2" name="pass2" type="password" value="" />
            </div>
        </fieldset>

        <fieldset class="form-actions">
            <input type="hidden" name="fpsubmitted" value="1" />

            <input type="submit" name="submit" class="btn btn-success" value="Submit" />
            <a class="btn" href="{genUrl controller="auth" action="login"}">Return to Login</a>
        </fieldset>

        </form>
    </div>
</div>

<p align="center">
    For help please contact {mailto address=$config.identity.email encode='javascript' text=$config.identity.name}.
</p>


{include file="footer.tpl"}
