{tmplinclude file="header.tpl"}

<div class="page-content">

    <div class="page-header">
        <h1>Login to IXP Manager</h1>
    </div>


{include file="message.tpl"}

<div class="row">

    <div class="span6 offset4">
        <img src="{genUrl}{$config.identity.ixp.biglogo}" />
    </div>
    
</div>

<div class="row">

    <div class="span6 offset3">
        <form class="form-horizontal" action="{genUrl controller="auth" action="process"}" method="post" name="loginForm" id="loginForm">

        <fieldset class="control-group" id="div-form-username">
            <label for="username" class="control-label">Username</label>
            <div id="div-controls-username" class="controls">
                <input id="username" name="loginusername" type="text" value="{if isset($username)}{$username}{/if}" />
            </div>            
        </fieldset>

        <fieldset class="control-group" id="div-form-password">
            <label for="password" class="control-label">Password</label>
            <div id="div-controls-password" class="controls">
                <input id="password" name="loginpassword" type="password" value="" />
            </div>            
        </fieldset>

        <fieldset class="form-actions">
            <input type="submit" name="submit" class="btn btn-success" value="Login" />
            <a class="btn" href="{genUrl controller="auth" action="forgotten-password"}">Forgotten Password?</a>
            <a class="btn" href="{genUrl controller="auth" action="forgotten-username"}">Forgotten Username?</a>
        </fieldset>

        </form>
    </div>

</div>

<p align="center">
    For help please contact {mailto address=$config.identity.email encode='javascript' text=$config.identity.name}.
</p>


{tmplinclude file="footer.tpl"}
