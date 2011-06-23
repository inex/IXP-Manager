
To whom it may concern,

You or someone purporting to be you has requested a password change
for our IXP Manager.

If you wish to proceed, please click on the following link:

http{if isset( $smarty.server.HTTPS ) and $smarty.server.HTTPS}s{/if}://{$smarty.server.SERVER_NAME}{genUrl controller="auth" action="reset-password" token=$token}




