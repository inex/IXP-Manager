#! /bin/sh

set -e

# Script for synchronising subscriptions between mailing lists and IXP Manager.
#
# Does not affect any subscriptions with email addresses that do not match a user
# in IXP Manager.
#
# Generated: <?= date( 'Y-m-d H:i:s' ) . "\n" ?>
#

# Please set the following as apprporiate for your environment
URL="https://ixp.example.com/api/v4/mailing-list"
KEY="MyKey"
TMP=/tmp

<?php foreach( $t->lists as $name => $ml ): ?>

############################################

# Set default subsciption settings for any new IXP Manager users
<?= config( 'mailinglists.mailman.cmds.list_members', 'XXX' ) ?> <?= $name ?> > $TMP/ml-<?= $name ?>.txt
curl -sf -H "X-IXP-Manager-API-Key: $KEY" \
    -X POST --data-urlencode addresses@$TMP/ml-<?= $name ?>.txt \
    $URL/init/<?= $name ?> >/dev/null
rm $TMP/ml-<?= $name ?>.txt

# Add new subscriptions to the list
curl -sf -H "X-IXP-Manager-API-Key: $KEY" \
    -X GET $URL/subscribers/<?= $name ?> \
    | <?= config( 'mailinglists.mailman.cmds.add_members', 'XXX' ) ?> <?= $name ?> >/dev/null

# Remove subscriptions from the list
curl -sf -H "X-IXP-Manager-API-Key: $KEY" \
    -X GET $URL/unsubscribed/<?= $name ?> \
    | <?= config( 'mailinglists.mailman.cmds.remove_members', 'XXX' ) ?> <?= $name ?> >/dev/null

<?php endforeach; ?>
