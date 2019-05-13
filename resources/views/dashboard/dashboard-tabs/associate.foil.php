<div class="col-12">
    <h4>Recent Members</h4>

    <div class="mb-4 tw-text-sm">
        Our five most recent members are listed below.

        <?php if( !$t->c->isTypeAssociate() ): ?>
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
            <?php foreach( $t->recentMembers as $recentMember ): ?>
                <tr>
                    <td>
                        <a href="<?= route( 'customer@detail', [ 'id' => $recentMember->getId() ] ) ?>">
                            <?= $t->ee( $recentMember->getName() ) ?>
                        </a>
                    </td>
                    <td class="tw-text-right tw-font-mono">
                        <?= $t->asNumber( $recentMember->getAutsys() ) ?>
                    </td>
                    <td>
                        <?php if( $recentMember->getDatejoin() ): ?>
                            <?= $recentMember->getDatejoin()->format( 'Y-m-d' ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
