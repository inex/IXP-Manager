<div class="table-responsive">
    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th>
                Name
            </th>
            <th>
                AS
            </th>
            <?php if( Auth::getUser()->isSuperUser() ): ?>
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
        <?php if( count( $t->c->getResoldCustomers() ) ): ?>
            <?php foreach( $t->c->getResoldCustomers() as $rc ): ?>
                <?php if( $rc->hasLeft() ): ?>
                    <?php continue; ?>
                <?php endif; ?>
                <tr>
                    <td>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <a href="<?= route( "customer@overview", [ "id" => $rc->getId() ] ) ?>">
                                <?= $t->ee( $rc->getName() ) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= route( "customer@detail", [ "id" => $rc->getId() ] ) ?>">
                                <?= $t->ee( $rc->getName() ) ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $t->asNumber( $rc->getAutsys() ) ?>
                    </td>
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <td>
                            <a href="<?= route( "customer@overview", [ "id" => $rc->getId() ] ) ?>">
                                <?= $t->ee( $rc->getShortname() ) ?>
                            </a>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= $t->ee(  $rc->getPeeringemail() ) ?>
                    </td>
                    <td>
                        <?= $t->ee( $rc->getNoc24hphone() ) ?>
                    </td>
                    <td>
                        <?= $rc->getDatejoin()->format('Y-m-d H:i:s') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td>
                    No resold customers found.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
