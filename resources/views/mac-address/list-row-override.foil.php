
<?php
// due to how PHP Foil passes data, we reassign this so we can copy and paste normal list code if we want.
// see http://www.foilphp.it/docs/DATA/PASS-DATA.html
$row = $t->row;
?>

<tr>

    <td>
        <?= $row['customer'] ?>
    </td>

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

    <td>
        <?= $row['ip4'] ?>
    </td>

    <td>
        <?= $row['ip6'] ?>
    </td>

    <td>
        <a id="view-l2a-<?= $row[ 'id' ] ?>" name="<?= $t->ee( $row[ 'mac' ] ) ?>" href="#" title="View">
            <?= $t->ee( $row[ 'mac' ] ) ?>
        </a>
    </td>

    <td>
        <?= $row['organisation'] ?>
    </td>


    <td>

        <div class="btn-group">

            <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

        </div>

    </td>

</tr>
