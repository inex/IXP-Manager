<div class="col-12">
    <h4>Recent Members</h4>
    <div class="mb-4 tw-text-sm">
        Our five most recent members are listed below.

        <?php if( !$t->c->typeAssociate() ): ?>
            Have you arranged peering with them yet?
        <?php endif; ?>
    </div>

    <table  class="table table-sm table-hover" >
        <thead class="thead-dark">
            <tr>
                <th>
                    Name
                </th>
                <th class="tw-text-right">
                    AS Number
                </th>
                <th>
                    Date Joined
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->recentMembers as $recent ): ?>
                <tr>
                    <td>
                        <a href="<?= route( 'customer@detail', [ 'cust' => $recent->id ] ) ?>">
                            <?= $t->ee( $recent->name ) ?>
                        </a>
                    </td>
                    <td class="tw-text-right tw-font-mono">
                        <?= $t->asNumber( $recent->autsys ) ?>
                    </td>
                    <td>
                        <?php if( $recent->datejoin ): ?>
                            <?= \Carbon\Carbon::instance( $recent->datejoin )->format( 'Y-m-d' ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>