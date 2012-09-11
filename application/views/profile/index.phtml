{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li class="active">
            <a href="{genUrl controller=$controller action=$action}">My Profile</a>
        </li>
    </ul>
{else}
    <div class="page-content">
        <div class="page-header">
            <h1>User Profile</h1>
        </div>
{/if}

{include file="message.tpl"}
<div id="ajaxMessage"></div>

<p>
This is your INEX user profile where you can change you contact preferences and password.
</p>

<p>
Please note that the mobile number is used to send you password reminders and should contain the country code
in the format: <code>353861234567</code>.
</p>

<div class="row-fluid">
    <div class="span6">

        <h3>Change Your Profile</h3>
        
        {$profileForm}
        
    </div>
    <div class="span6">

        <h3>Change Your Password</h3>
        
        {$passwordForm}
        
    </div>
</div>

{if $mailinglist_enabled}
    <div class="row-fluid">
        <div class="span6">
            <h3>Your Mailing List Subscriptions</h3>
            <p>
                <br />
                {$config.identity.orgname} operates the below mailing lists to help us interact with our
                members and for our members to interact with each other.
            </p>
            <p>
                There are also links below to the list archives - for which your username is
                {$user.email} and your password is the same as your IXP Manager password.
            </p>
            <p>
                The below are your subscriptions for <strong>{$user.email}</strong>.
            </p>
            <br />
            <form action="{genUrl controller="profile" action="update-mailing-lists"}" method="post" class="form">
            <fieldset>

                {foreach from=$mailinglists key=name item=ml}

	            {if $customer.type neq Cust::TYPE_ASSOCIATE or ( isset( $ml.associates ) and $ml.associates )}

                        <div class="control-group">
                            <label class="checkbox">
                                <input type="checkbox" name="ml_{$name}" value="1" {if $ml.subscribed}checked="checked"{/if}>
                                    <strong>{$ml.name}</strong> - {$ml.desc}
                                    ({if $ml.email}{mailto address=$ml.email} - {/if}<a href="{$ml.archive}">archives</a>)
                            </label>
                        </div>
                    {/if}

                {/foreach}
            
                <div class="form-actions">
                    <input type="submit" class="btn btn-primary" value="Update My Subscriptions" id="submit" name="submit">
                </div>
            
            </fieldset>
            </form>
            
        </div>
        
        <div class="span6">
            &nbsp;
        </div>
    </div>
{/if}

{include file="footer.tpl"}
