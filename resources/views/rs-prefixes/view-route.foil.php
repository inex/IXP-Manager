<table class="table collapse table-striped w-100" id="list-table-<?= $t->type ?>" width="100%">
    <thead class="thead-dark">
        <th>
            Prefix
        </th>
        <th>
            Protocol
        </th>
        <th>
            First Seen
        </th>
        <?php if( $t->type === 'adv_acc' || $t->type === 'adv_nacc' ): ?>
            <th>
                Origin AS
            </th>
        <?php endif; ?>
    </thead>
    <tbody>
        <?php foreach( $t->aggRoutes[ $t->type ] as $r ): ?>
            <tr>
                <td>
                    <?= $t->ee( $r[ 'prefix'] ) ?>
                </td>
                <td>
                    IPv<?= $t->ee( $r[ 'protocol'] ) ?>
                </td>
                <td>
                    <?= $r[ 'timestamp'] ?>
                </td>
                <?php if( $t->type === 'adv_acc' || $t->type === 'adv_nacc' ): ?>
                    <td>
                        <?= $t->asNumber( $r[ 'rsorigin' ] ) ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>