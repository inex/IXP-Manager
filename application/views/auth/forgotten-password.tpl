{include file="header.tpl"}

<div class="page-content">

    <div class="page-header">
        <h1>Forgotten Password</h1>
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
            Please enter your username and we will send you a password reset token by email.
        </p>
        
        <form class="form-horizontal" action="{genUrl controller="auth" action="forgotten-password"}" method="post" name="loginForm" id="loginForm">

        <fieldset class="control-group" id="div-form-username">
            <label for="username" class="control-label">Username</label>
            <div id="div-controls-username" class="controls">
                <input id="username" name="loginusername" type="text" value="{if isset($username)}{$username}{/if}" />
            </div>            
        </fieldset>

        <fieldset class="form-actions">
            <input type="hidden" name="fpsubmitted" value="1" />

            <input type="submit" name="submit" class="btn btn-success" value="Submit" />
            <a class="btn" href="{genUrl controller="auth" action="forgotten-username"}">Forgotten Username?</a>
            <a class="btn" href="{genUrl controller="auth" action="login"}">Return to Login</a>
        </fieldset>

        </form>
    </div>
</div>

<p align="center">
    For help please contact {mailto address=$config.identity.email encode='javascript' text=$config.identity.name}.
</p>

{include file="footer.tpl"}
