#! /bin/sh

set -e

#
# Script for synchronising subscriptions between mailing lists and IXP Manager.
#
# Does not affect any subscriptions with email addresses that do not match a user
# in IXP Manager.
#
# Generated: <?= date( 'Y-m-d H:i:s' ) . "\n" ?>
#

# Please set the following as apprporiate for your environment
IXPROOT=/srv/ixpmanager

<?php foreach( $t->lists as $name => $ml ): ?>

#######################################################################################################################################
##
## <?= $name ?> - <?= $ml['name'] . "\n" ?>
##

# Set default subsciption settings for any new IXP Manager users
<?= config( 'mailinglists.mailman.cmds.list_members', 'XXX' ) ?> <?= $name ?> | php $IXPROOT/artisan mailing-list:init <?= $name . "\n" ?>

# Add new subscriptions to the list
php $IXPROOT/artisan mailing-list:get-subscribers <?= $name ?> | <?= config( 'mailinglists.mailman.cmds.add_members', 'XXX' ) ?> <?= $name ?> >/dev/null

# Remove subscriptions from the list
php $IXPROOT/artisan mailing-list:get-subscribers <?= $name ?> --unsubscribed | <?= config( 'mailinglists.mailman.cmds.remove_members', 'XXX' ) ?> <?= $name ?> >/dev/null

<?php endforeach; ?>


