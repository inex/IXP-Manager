#! /bin/sh

#
# Script for syncronising subscriptions between mailing lists and IXP Manager.
#
# Does not affect any subscriptions with email addresses that do not match a user
# in IXP Manager.
#
# Generated: {$date}
#

# Please set the following as apprporiate for your environment
URL="https://www.example.com/ixp"
KEY="MyKey"

{foreach $options.mailinglists as $name => $ml}

#######################################################################################################################################
##
## {$name} - {$ml.name}
##

# Set default subsciption settings for any new IXP Manager users
{$options.mailinglist.cmd.list_members} {$name} >{$apppath}/../var/tmp/ml-{$name}.txt
curl -sf --data-urlencode addresses@{$apppath}/../var/tmp/ml-{$name}.txt \
    "$URL/apiv1/mailing-list/init/key/$KEY/list/{$name}"
rm {$apppath}/../var/tmp/ml-{$name}.txt

# Add new subscriptions to the list
curl -sf "$URL/apiv1/mailing-list/get-subscribed/key/$KEY/list/{$name}" | \
    {$options.mailinglist.cmd.add_members} {$name} >/dev/null

# Remove subscriptions from the list
curl -sf "$URL/apiv1/mailing-list/get-unsubscribed/key/$KEY/list/{$name}" | \
    {$options.mailinglist.cmd.remove_members} {$name} >/dev/null

# Sync passwords
curl -sf "$URL/apiv1/mailing-list/password-sync/key/$KEY/list/{$name}" | \
    egrep "^{$options.mailinglist.cmd.changepw} '.+' '.+' '.+'$" | /bin/sh >/dev/null

{/foreach}


