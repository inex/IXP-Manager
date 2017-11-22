<br>
<table class="table collapse" id="summary-table-<?= $t->type ?>">
    <thead>
        <th>Customer</th>
        <th>IPv4</th>
        <th>IPv6</th>
        <th>Total</th>
    </thead>
    <tbody>
        <?php foreach( $t->cust_prefixes as $id => $cp ): ?>
            <?php if( $cp[ $t->type ][ 'total' ] > 0 ): ?>
                <tr>
                    <td>
                        <a href="<?= url( "customer/overview/id/" ).'/'.$id ?>">
                            <?= $cp[ 'name' ] ?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cid' => $id, 'type' => $t->type, 'protocol' => 4 ] ) ?>">
                            <?= $cp[ $t->type ][ 4 ]?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cid' => $id, 'type' => $t->type, 'protocol' => 6 ] ) ?>">
                            <?= $cp[ $t->type ][ 6 ]?>
                        </a>
                    </td>
                    <td>
                        <a href="<?= route( "rs-prefixes@view", [ 'cid' => $id, 'type' => $t->type ] ) ?>">
                            <?= $cp[ $t->type ][ 'total' ]?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>