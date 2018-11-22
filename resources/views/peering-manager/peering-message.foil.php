Dear <?= $t->peer->getName() ?> Peering Team,

We are <?= $t->user->getCustomer()->getName() ?> (<?= $t->user->getCustomer()->getCorpwww() ?>) and we are fellow members of <?= config( "identity.orgname" ) ?> (<?= config( "identity.location.city" ) ?>, <?= config( "identity.location.country") ?>).

We would like to arrange peering session(s) with you on the following interface(s):

<?php foreach( $t->pp as $p ): ?>

<?php $pmy = $p[ "my" ] ?>
<?php $pyour = $p[ "your" ] ?>

### <?= $pmy->getVlan()->getName() ?>


```
Our AS Number:     <?= $t->user->getCustomer()->getAutsys() ?>

<?php if( $pmy->getIpv4enabled() && $pyour->getIpv4enabled() ): ?>
Our IPv4 Address:  <?= $pmy->getIpv4address()->getAddress() ?>

<?php if( $t->user->getCustomer()->getPeeringmacro() ): ?>
Our IPv4 AS Macro: <?= $t->user->getCustomer()->getPeeringmacro() ?>

<?php endif; ?>
<?php endif; ?>

<?php if( $pmy->getIpv6enabled() && $pyour->getIpv6enabled() ): ?>
Our IPv6 Address:  <?= $pmy->getIpv6address()->getAddress() ?>

Our IPv6 AS Macro: <?= $t->user->getCustomer()->resolveAsMacro( 6 ) ?>
<?php endif; ?>

```


```
<?php if( $pmy->getIpv4enabled() && $pyour->getIpv4enabled() ): ?>
Your IPv4 Address: <?= $pyour->getIpv4address()->getAddress() ?>

<?php endif; ?>
<?php if( $pmy->getIpv6enabled() && $pyour->getIpv6enabled() ): ?>
Your IPv6 Address: <?= $pyour->getIpv6address()->getAddress() ?>

<?php endif; ?>
Your AS Number:    <?= $t->peer->getAutsys() ?>

```

<?php endforeach; ?>



### NOC Details for <?= $t->user->getCustomer()->getName() ?>


The following are our NOC details for your reference:

* NOC Hours: <?= $t->user->getCustomer()->getNochours() ?>

* NOC Phone: <?= $t->user->getCustomer()->getNocphone() ?>

<?php if( $t->user->getCustomer()->getNoc24hphone() ): ?>
* NOC 24h Phone: <?= $t->user->getCustomer()->getNoc24hphone() ?>

<?php endif; ?>
<?php if( $t->user->getCustomer()->getNocfax() ): ?>
* NOC Fax:       <?= $t->user->getCustomer()->getNocfax() ?>

<?php endif; ?>
* NOC Email:     <?= $t->user->getCustomer()->getNocemail() ?>

<?php if( $t->user->getCustomer()->getNocwww() ): ?>
* NOC WWW:       <?= $t->user->getCustomer()->getNocwww() ?>

<?php endif; ?>


Kind regards,

The <?= $t->user->getCustomer()->getName() ?> Peering Team


--

**NB:** the *From:* address of this email is a <?= config( "identity.orgname" ) ?> blackhole address to avoid SPAM tagging. The *Reply-To:* header is correctly set to [<?= $t->user->getCustomer()->getPeeringemail() ?>](<?= $t->user->getCustomer()->getPeeringemail() ?>). If your ticketing / email system does not pick this up automatically, please be sure to set it.


This email was composed with the assistance of the <?= config( "identity.orgname") ?> Peering Manager which is part of your members area at: <?= config( "app.url" ) ?>.
