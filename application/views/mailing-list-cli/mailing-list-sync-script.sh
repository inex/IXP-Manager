#! /bin/sh

#
# Script for syncronising subscriptions between mailing lists and IXP Manager.
#
# Does not affect any subscriptions with email addresses that do not match a user
# in IXP Manager.
#
# Generated: {$date}
#

{foreach $options.mailinglists as $name => $ml}

#######################################################################################################################################
##
## {$name} - {$ml.name}
##

# Set default subsciption settings for any new IXP Manager users
{$options.mailinglist.cmd.list_members} {$name} | {$apppath}/../bin/ixptool.php -a mailing-list-cli.list-init --p1={$name}

# Add new subscriptions to the list
{$apppath}/../bin/ixptool.php -a mailing-list-cli.get-subscribed --p1={$name} | {$options.mailinglist.cmd.add_members} {$name} >/dev/null

# Remove subscriptions from the list
{$apppath}/../bin/ixptool.php -a mailing-list-cli.get-unsubscribed --p1={$name} | {$options.mailinglist.cmd.remove_members} {$name} >/dev/null

# Sync passwords
{$apppath}/../bin/ixptool.php -a mailing-list-cli.password-sync --p1={$name} >/dev/null


{/foreach}


