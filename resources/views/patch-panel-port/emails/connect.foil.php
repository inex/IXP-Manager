
Hi,

** ACTION REQUIRED - PLEASE SEE BELOW **

We have allocated the following cross connect demarcation point for your connection to
<?= env( 'IDENTITY_ORGNAME' ) ?>. Please order a <?= $t->ppp->getPatchPanel()->getCableType() ?>
cross connect where our demarcation point is:

```
Patch panel:    <?= $t->ppp->getPatchPanel()->getName() ?>

Port:           <?= $t->ppp->getName() ?>
```

<?php if( $t->ppp->getSwitchPort() ): ?>
This request is in relation the following connection:

```
Switch Port:   <?= $t->ppp->getSwitchName() ?>::<?= $t->ppp->getSwitchPortName() ?>

```

If you have any queries about this, please reply to this email.


