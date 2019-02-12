<div class="col-sm-12">
    <h3>Recent Members</h3>

    <div class="mb-4">
        Our five most recent members are listed below.

        <?php if( !$t->c->isTypeAssociate() ): ?>
            Have you arranged peering with them yet?
        <?php endif; ?>
    </div>


    <table  class="table-responsive-ixp table table-striped collapse" style="width: 100%">
        <thead class="thead-dark">
            <tr>
                <th>
                    Name
                </th>
                <th>
                    AS Number
                </th>
                <th>
                    Date Joined
                </th>
                <?php if( !$t->c->isTypeAssociate() ): ?>
                    <th>
                        Peering Contact
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $t->recentMembers as $recentMember ): ?>
                <tr>
                    <td>
                        <?= $t->ee( $recentMember->getName() ) ?>
                    </td>
                    <td>
                        <?= $t->asNumber( $recentMember->getAutsys() ) ?>
                    </td>
                    <td>
                        <?php if( $recentMember->getDatejoin() ): ?>
                            <?= $recentMember->getDatejoin()->format( 'Y-m-d' ) ?>
                        <?php endif; ?>
                    </td>
                    <?php if( !$t->c->isTypeAssociate() ): ?>
                        <td>
                            <?php if( $recentMember->getpeeringemail() ): ?>
                                <a href="mailto:<?= $t->ee( $recentMember->getpeeringemail() ) ?>" > <?= $t->ee( $recentMember->getpeeringemail() ) ?> </a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
