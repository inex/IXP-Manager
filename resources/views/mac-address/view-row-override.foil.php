<?php
    // due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
    // see http://www.foilphp.it/docs/DATA/PASS-DATA.html
    $row = $t->data[ 'item' ];
?>
<tr>
    <th>
        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
    </th>
    <td>
        <a href="<?= route( "customer@overview" , [ 'cust' => $row[ 'customerid' ] ] ) ?>">
            <?= $t->ee( $row['customer'] ) ?>
        </a>
    </td>
</tr>
<tr>
    <th>
        Interface
    </th>
    <td>
        <?php if( strpos( $row['switchport'], ',' ) !== false ) {
            $ports = explode( ',', $row['switchport'] );
            asort( $ports, SORT_NATURAL );

            foreach( $ports as $port ) {
                echo "<a href=".route('virtual-interface@edit', [ 'vi' => $row['viid'] ] ).">".$t->ee( $row['switchname'] ) . '::' . $t->ee( $port )."</a><br/>";
            }
        } else {
            echo "<a href=".route('virtual-interface@edit', [ 'vi' => $row['viid'] ] ).">".$t->ee( $row['switchname'] ) . '::' . $t->ee( $row[ 'switchport' ] ) ."</a>";
        } ?>
    </td>
</tr>
<tr>
    <th>
        IPv4
    </th>
    <td>
        <?= $t->ee( $row['ip4'] ) ?>
    </td>
</tr>
<tr>
    <th>
        IPv6
    </th>
    <td>
        <?= $t->ee( $row['ip6'] ) ?>
    </td>
</tr>
<tr>
    <th>
        MAC Address
    </th>
    <td>
        <a id="view-l2a-<?= $row[ 'id' ] ?>" class="btn-view-l2a" data-object-mac="<?= $t->ee( $row[ 'mac' ] ) ?>" href="#" title="View">
            <?= $t->ee( $row[ 'mac' ] ) ?>
        </a>
    </td>
</tr>
<tr>
    <th>
        Manufacturer
    </th>
    <td>
        <?= $t->ee( $row['organisation'] ) ?>
    </td>
</tr>
<tr>
    <th>
        Created
    </th>
    <td>
        <?= $row[ 'created_at' ] ? Carbon\Carbon::parse( $row[ 'created_at' ] ) : '' ?>
    </td>
</tr>
    <tr>
        <th>
            Updated
        </th>
        <td>
            <?= $row[ 'updated_at' ] ? Carbon\Carbon::parse( $row[ 'updated_at' ] ) : '' ?>
        </td>
    </tr>
<?= $t->insert( 'layer2-address/modal-mac' ); ?>