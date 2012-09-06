
To whom it may concern,

You or someone purporting to be you has requested a username reminder
for our IXP Manager.

The usernames linked to your account are:

{foreach from=$users item=u}
 * {$u.username}
{/foreach}

If you did not make this request, please ignore this mail.

Thanks and kind regards,

{$config.identity.name}
{$config.identity.email}

