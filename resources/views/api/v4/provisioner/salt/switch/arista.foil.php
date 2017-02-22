<?php
/** @var \Entities\SwitchPort $sp */
/** @var \Entities\PhysicalInterface $pi */
/** @var \Entities\VirtualInterface $vi */
/** @var \Entities\VlanInterface $vli */
/** @var \Entities\MACAddress $mac */
?>
interfacescust:

<?php
    $lagsProcessed = [];
    foreach( $t->switch->getPorts() as $sp ):

        if( !$sp->isTypePeering() ) {
            continue;
        }

        if( !( $pi = $sp->getPhysicalInterface() ) ) {
            continue;
        }
        $vi = $pi->getVirtualInterface();

?>
  - name: <?= $sp->getIfName() ?>

    description: "Cust: <?= $vi->getCustomer()->getAbbreviatedName() ?>"
    dot1q: <?= $vi->getTrunk() ? 'yes' : 'no' ?>

    speed: <?= $pi->getSpeed() ?>

<?php if( $vi->getChannelgroup() ): ?>
    lagindex: <?= $vi->getChannelgroup() ?>

<?php endif; ?>
    virtualinterfaceid: <?= $vi->getId() ?>

    vlans:
<?php foreach( $vi->getVlanInterfaces() as $vli ): ?>
      -
        number: <?= $vli->getVlan()->getNumber() . "\n" ?>
<?php endforeach; ?>
        macaddresseses:
<?php foreach( $vi->getMACAddresses() as $mac ): ?>
          - "<?= $mac->getMacFormattedWithColons() ?>"
<?php endforeach; ?>

<?php endforeach; ?>


# port type: multiple vlan, tagged, no lacp
- name: Ethernet2
description: "Cust: We Eat Customers ISP Ltd"
speed: 1000
dot1q: yes
virtualinterfaceid: 1235
vlans:
-
number: 10
macaddress:
- "12:34:56:78:90:ab"
- "cd:ef:12:34:56:78"
-
number: 30
macaddress:
- "66:55:44:33:22:11"
- "aa:bb:cc:dd:ee:ff"

# port type: multiple vlan, tagged, no 802.3ad
- name: Ethernet3
description: "Cust: Drop Ur Data Ltd"
speed: 10000
virtualinterfaceid: 932
dot1q: yes
vlans:
- number: 10
macaddress:
- "aa:bb:cc:ee:dd:11"
- number: 3079

# LAG member
- name: Ethernet5
description: "Cust: Lolsecurity ISP Ltd"
speed: 10000
lagindex: 22
dot1q: yes
virtualinterfaceid: 1299
vlans:
-
number: 10
macaddress:
- "12:34:56:78:90:ab"
- "cd:ef:12:34:56:78"
-
number: 30
macaddress:
- "66:55:44:33:22:11"
- "aa:bb:cc:dd:ee:ff"

# port type: multiple vlan, tagged, 802.3ad
- name: Port-Channel22
description: "Cust: Lolsecurity ISP Ltd"
dot1q: yes
lagindex: 22
lagmaster: yes
virtualinterfaceid: 1299
vlans:
-
number: 10
macaddress:
- "12:34:56:78:90:ab"
- "cd:ef:12:34:56:78"
-
number: 30
macaddress:
- "66:55:44:33:22:11"
- "ab:bb:cc:dd:ee:ff"