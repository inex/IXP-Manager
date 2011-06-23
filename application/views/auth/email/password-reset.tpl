
To whom it may concern,

You or someone purporting to be you has requested a password change
for our IXP Manager.

If you wish to proceed, please click on the following link:

http{if isset( $smarty.server.HTTPS ) and $smarty.server.HTTPS}s{/if}://{$smarty.server.SERVER_NAME}{genUrl controller="auth" action="reset-password" token=$token}

Please note that this password reset request invalidates all previous requests.

If you did not make this request, please ignore this mail.

Thanks and kind regards,

{$config.identity.name}
{$config.identity.email}

