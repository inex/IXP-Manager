<table class="table collapse table-striped w-100" id="summary-table-<?= $t->type ?>">
    <thead class="thead-dark">
        <th>
            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
        </th>
        <th>
            IPv4
        </th>
        <th>
            IPv6
        </th>
        <th>
            Total
        </th>
    </thead>
    <tbody>
        <?php foreach( $t->cPrefixes as $id => $cp ): ?>
            <?php if( $cp[ $t->type ][ 'total' ] > 0 ): ?>
                <tr>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cust' => $id ] ) ?>">
                            <?= $cp[ 'name' ] ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cust' => $id, 'type' => $t->type , 'protocol' => 4] ) ?>">
                            <?= $cp[ $t->type ][ 4 ]?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cust' => $id, 'type' => $t->type , 'protocol' => 6 ] ) ?>">
                            <?= $cp[ $t->type ][ 6 ]?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cust' => $id, 'type' => $t->type ] ) ?>">
                            <?= $cp[ $t->type ][ 'total' ]?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
