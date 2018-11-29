<div class="col-sm-12">
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Colocation Circuit Ref</th>
                <?php if( Auth::getUser()->isSuperUser() ): ?>
                    <th>Ticket Ref</th>
                <?php endif; ?>
                <th>State</th>
                <th>Cabinet</th>
                <th>Location</th>
                <th>Assigned At</th>
                <th>Chargeable</th>
                <th>Owned By</th>
            </tr>
        </thead>
        <tbody>
            <?php if( count( $t->crossConnects) > 0 ): ?>
                <?php foreach( $t->crossConnects as $patchPanelPort ): ?>
                    <tr>
                        <td>
                            <a href="<?= route( "patch-panel-port@view" , [ "id" => $patchPanelPort->getId() ] ) ?>">
                                <?= $t->ee($patchPanelPort->getPatchPanel()->getName() ) ?>
                                <?= $t->ee( $patchPanelPort->getName() ) ?>
                            </a>
                        </td>
                        <td>
                            <?= $t->ee( $patchPanelPort->getColoCircuitRef() ) ?>
                        </td>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <td>
                                <?= $t->ee( $patchPanelPort->getTicketRef() ) ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php $class = $patchPanelPort->getStateCssClass() ?>

                            <span title="" class="label label-<?=$class ?>">
                            <?= $patchPanelPort->resolveStates() ?>
                            </span>
                        </td>
                        <td>
                            <?= $t->ee( $patchPanelPort->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $patchPanelPort->getPatchPanel()->getCabinet()->getName() ) ?>
                        </td>
                        <td>
                            <?= $patchPanelPort->getAssignedAtFormated() ?>
                        </td>
                        <td>
                            <?= $patchPanelPort->resolveChargeable() ?>
                        </td>
                        <td>
                            <?= $patchPanelPort->resolveOwnedBy() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>No Patch Panel available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
