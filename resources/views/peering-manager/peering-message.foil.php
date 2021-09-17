Dear <?= $t->peer->name ?> Peering Team,

We are <?= $t->user->customer->name ?> (<?= $t->user->customer->corpwww ?>) and we are fellow members of <?= config( "identity.orgname" ) ?> (<?= config( "identity.location.city" ) ?>, <?= config( "identity.location.country") ?>).

We would like to arrange peering session(s) with you on the following interface(s):

<?php foreach( $t->pp as $p ): ?>

<?php $pmy = $p[ "my" ] /** @var $pmy \IXP\Models\VlanInterface */ ?>
<?php $pyour = $p[ "your" ] /** @var $pyour \IXP\Models\VlanInterface */ ?>

### <?= $pmy->vlan->name ?>


```
Our AS Number:     <?= $t->user->customer->autsys ?>

<?php if( $pmy->ipvxEnabled( 4 ) && $pyour->ipvxEnabled( 4 ) ): ?>
Our IPv4 Address:  <?= $pmy->ipv4address->address ?>

<?php if( $t->user->customer->peeringmacro ): ?>
Our IPv4 AS Macro: <?= $t->user->customer->peeringmacro ?>

<?php endif; ?>
<?php endif; ?>

<?php if( $pmy->ipvxEnabled( 6 ) && $pyour->ipvxEnabled( 6 ) ): ?>
Our IPv6 Address:  <?= $pmy->ipv6address->address ?>

Our IPv6 AS Macro: <?= $t->user->customer->asMacro( 6 ) ?>
<?php endif; ?>


<?php if( $t->user->customer->in_peeringdb ): ?>
We're on PeeringDB: https://www.peeringdb.com/asn/<?= $t->user->customer->autsys ?>
<?php endif; ?>

```


```
<?php if( $pmy->ipvxEnabled( 4 ) && $pyour->ipvxEnabled( 4 ) ): ?>
Your IPv4 Address: <?= $pyour->ipv4address->address ?>

<?php endif; ?>
<?php if( $pmy->ipvxEnabled( 6 ) && $pyour->ipvxEnabled( 6 ) ): ?>
Your IPv6 Address: <?= $pyour->ipv6address->address ?>

<?php endif; ?>
Your AS Number:    <?= $t->peer->autsys ?>

```

<?php endforeach; ?>



### NOC Details for <?= $t->user->customer->name ?>


The following are our NOC details for your reference:

* NOC Hours: <?= $t->user->customer->nochours ?>

* NOC Phone: <?= $t->user->customer->nocphone ?>

<?php if( $t->user->customer->noc24hphone ): ?>
* NOC 24h Phone: <?= $t->user->customer->noc24hphone ?>

<?php endif; ?>
<?php if( $t->user->customer->nocfax ): ?>
* NOC Fax:       <?= $t->user->customer->nocfax ?>

<?php endif; ?>
* NOC Email:     <?= $t->user->customer->nocemail ?>

<?php if( $t->user->customer->nocwww ): ?>
* NOC WWW:       <?= $t->user->customer->nocwww ?>

<?php endif; ?>


Kind regards,

The <?= $t->user->customer->name ?> Peering Team


--

**NB:** the *From:* address of this email is a <?= config( "identity.orgname" ) ?> blackhole address to avoid SPAM tagging. The *Reply-To:* header is correctly set to [<?= $t->user->customer->peeringemail ?>](<?= $t->user->customer->peeringemail ?>). If your ticketing / email system does not pick this up automatically, please be sure to set it.


This email was composed with the assistance of the <?= config( "identity.orgname") ?> Peering Manager which is part of your members area at: <?= config( "app.url" ) ?>.
