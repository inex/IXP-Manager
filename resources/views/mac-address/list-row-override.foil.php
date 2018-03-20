
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->row;
?>

<tr>

    <td>
        <a href="<?=  route( "customer@overview" , [ "id" => $row[ 'customerid' ] ] ) ?>">
            <?= $t->ee( $row['customer'] ) ?>
        </a>
    </td>

    <td>
        <?php if( strpos( $row['switchport'], ',' ) !== false ) {

            $ports = explode( ',', $row['switchport'] );
            asort( $ports, SORT_NATURAL );

            foreach( $ports as $port ) {
                echo "<a href=".route('interfaces/virtual/edit', [ 'id' => $row['viid'] ] ).">". $t->ee( $row['switchname'] ) . '::' . $t->ee( $port )."</a><br/>";
            }

        } else {

            echo "<a href=".route('interfaces/virtual/edit', [ 'id' => $row['viid'] ] ).">". $t->ee( $row['switchname'] ) . '::' . $t->ee( $row[ 'switchport' ] ) ."</a>";

        } ?>
    </td>

    <td>
        <?= $t->ee( $row['ip4'] ) ?>
    </td>

    <td>
        <?= $t->ee( $row['ip6'] ) ?>
    </td>

    <td>
        <a id="view-l2a-<?= $row[ 'id' ] ?>" name="<?= $t->ee( $row[ 'mac' ] ) ?>" href="#" title="View">
            <?= $t->ee( $row[ 'mac' ] ) ?>
        </a>
    </td>

    <td>
        <?= $t->ee( $row['organisation'] ) ?>
    </td>


    <td>

        <div class="btn-group">

            <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

        </div>

    </td>

</tr>
