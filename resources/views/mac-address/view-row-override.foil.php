
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->data[ 'data' ];
?>

<tr>
    <th>
        Customer
    </th>
    <td>
        <?= $row['customer'] ?>
    </td>
<tr/>

<tr>
    <th>
        Interface
    </th>
    <td>
        <?php if( strpos( $row['switchport'], ',' ) !== false ) {

            $ports = explode( ',', $row['switchport'] );
            asort( $ports, SORT_NATURAL );

            foreach( $ports as $port ) {
                echo $row['switchname'] . '::' . $port . '<br>';
            }

        } else {

            echo $row['switchname'] . '::' . $row[ 'switchport' ];

        } ?>
    </td>
<tr/>

<tr>
    <th>
        IPv4
    </th>
    <td>
        <?= $row['ip4'] ?>
    </td>
<tr/>

<tr>
    <th>
        IPv6
    </th>
    <td>
        <?= $row['ip6'] ?>
    </td>
<tr/>

<tr>
    <th>
        MAC Address
    </th>
    <td>
        <a id="view-l2a-<?= $row[ 'id' ] ?>" name="<?= $t->ee( $row[ 'mac' ] ) ?>" href="#" title="View">
            <?= $t->ee( $row[ 'mac' ] ) ?>
        </a>
    </td>
<tr/>

<tr>
    <th>
        Manufacturer
    </th>
    <td>
        <?= $row['organisation'] ?>
    </td>
<tr/>
<?= $t->insert( 'layer2-address/modal-mac' ); ?>
