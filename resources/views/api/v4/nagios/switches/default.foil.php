#
# This file contains static definitions for use with the IXP Manager
# Nagios configuration templates and is following by dynamic host
# configurations.
#
#  Please see: https://github.com/inex/IXP-Manager/wiki/Nagios
#
# You should not need to edit these files - instead use your own custom skins. If
# you can't effect the changes you need with skinning, consider posting to the mailing
# list to see if it can be achieved / incorporated.
#
# Skinning: https://github.com/inex/IXP-Manager/wiki/Skinning
#


<?php

    // some vars for later:
    $locations      = [];
    $vendors        = [];
    $vendorSwitches = [];
    $all            = [];

    /** @var Entities\Switcher $s */
    foreach( $t->switches as $s ):

        $vendors[ $s->getVendor()->getShortname() ][] = $s;

        $vendorSwitches[ $s->getVendor()->getShortname() ][] = $s->getName();
        $locations[ $s->getCabinet()->getLocation()->getShortname() ][] = $s->getName();
        $all[] = $s->getName();

        ?>

#
# <?= $s->getName() ?> - <?= $s->getCabinet()->getCololocation() ?>, <?= $s->getCabinet()->getLocation()->getName() ?>.
#

define host {
    use                     ixp-production-switch
    host_name               <?= $s->getName() ?>

    alias                   <?= $s->getName() ?>

    address                 <?= $s->getIpv4addr() ?>

}

<?php endforeach; ?>

<?php foreach( $locations as $name => $switches ): ?>

define hostgroup {
    hostgroup_name          IXP-Switches-infraid<?= $t->infra->getId() ?>-<?= $name ?>

    alias                   IXP Switches at <?= $name ?> on <?= $t->infra->getName() ?>

    members                 <?= $t->softwrap( $switches, 1, ', ', ',', 28 ) ?>

}

<?php endforeach; ?>


define hostgroup {
    hostgroup_name          IXP-Production-Switches-infraid<?= $t->infra->getId() ?>

    alias                   IXP Production Switches (all on infraid<?= $t->infra->getName() ?>)
    members                 <?= $t->softwrap( $all, 1, ', ', ',', 28 ) ?>
}

<?php foreach( $vendors as $shortname => $v ): ?>

define hostgroup {
    hostgroup_name          IXP-Switches-infraid<?= $t->infra->getId() ?>-{$shortname}
    alias                   IXP {$shortname} Switches
    members                 {$vendor_strings.$shortname}

}

<?php endforeach; ?>


<?php foreach( $vendors as $shortname => $v ): ?>

define service{
    use                             ixp-production-switch-service
    hostgroup_name                  IXP-Switches-infraid<?= $t->infra->getId() ?>-{$shortname}
    service_description             Chassis
    check_command                   check_<?= $v[0]->getVendor()->getNagiosName() ?>_chassis!<?= $v[0]->getSnmppasswd() ?>

}


    <?php if( $shortname == 'Cisco'  ): ?>

define service  {
    use                             ixp-production-infraid<?= $t->infra->getId() ?>-switch-service
    service_description             Temperature
    hostgroup_name                  IXP-Switches-infraid<?= $t->infra->getId() ?>-<?= $shortname ?>
    check_command                   check_<?= $v[0]->getVendor()->getNagiosName() ?>_temperature!<?= $v[0]->getSnmppasswd() ?>!32!38
}

    <?php endif; ?>

<?php endforeach; ?>


define service{
    use                             ixp-production-infraid<?= $t->infra->getId() ?>-switch-service
    hostgroup_name                  IXP-Production-Switches
    service_description             ping - IPv4
    check_command                   check_ping_ipv4!10!100.0,10%!200.0,20%
}

define service  {
    use                             ixp-production-infraid<?= $t->infra->getId() ?>-switch-service
    service_description             SSH
    hostgroup_name                  IXP-Production-Switches
    check_command                   check_ssh
}


