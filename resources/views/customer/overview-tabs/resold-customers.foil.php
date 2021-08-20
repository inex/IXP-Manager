<table class="w-100 table table-striped table-responsive-ixp collapse">
    <thead class="thead-dark">
        <tr>
            <th>
                Name
            </th>
            <th>
                AS
            </th>
            <?php if( $t->isSuperUser ): ?>
                <th>
                    Shortname
                </th>
            <?php endif; ?>
            <th>
                Peering Email
            </th>
            <th>
                NOC 24h Phone
            </th>
            <th>
                Joined
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $t->c->resoldCustomers as $rc ): ?>
            <?php if( $rc->hasLeft() ): ?>
                <?php continue; ?>
            <?php endif; ?>
            <tr>
                <td>
                    <a href="<?= route( $t->isSuperUser ? 'customer@overview' : 'customer@detail', [ 'cust' => $rc->id ] ) ?>">
                        <?= $t->ee( $rc->name ) ?>
                    </a>
                </td>
                <td>
                    <?= $t->asNumber( $rc->autsys ) ?>
                </td>
                <?php if( $t->isSuperUser ): ?>
                    <td>
                        <a href="<?= route( "customer@overview", [ 'cust' => $rc->id ] ) ?>">
                            <?= $t->ee( $rc->shortname ) ?>
                        </a>
                    </td>
                <?php endif; ?>
                <td>
                    <?= $t->ee(  $rc->peeringemail ) ?>
                </td>
                <td>
                    <?= $t->ee( $rc->noc24hphone ) ?>
                </td>
                <td>
                    <?= $rc->datejoin ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>