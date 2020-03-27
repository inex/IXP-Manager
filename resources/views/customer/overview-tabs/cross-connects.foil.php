<div class="card mb-4">
    <div class="card-header d-flex">
        <h3>
            Cross Connect
        </h3>
    </div>
    <div class="card-body">
        <table class="table table-striped table-responsive-ixp collapse" style="width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Colocation Circuit Ref
                    </th>
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <th>
                            Ticket Ref
                        </th>
                    <?php endif; ?>
                    <th>
                        State
                    </th>
                    <th>
                        Location
                    </th>
                    <?php if( Auth::getUser()->isSuperUser() ): ?>
                        <th>
                            Cabinet
                        </th>
                    <?php endif; ?>
                    <th>
                        Assigned At
                    </th>
                    <th>
                        Chargeable
                    </th>
                    <th>
                        Owned By
                    </th>
                </tr>
            </thead>
            <tbody>
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

                            <span title="" class="badge badge-<?=$class ?>">
                            <?= $patchPanelPort->resolveStates() ?>
                            </span>
                        </td>
                        <td>
                            <?= $t->ee( $patchPanelPort->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                        </td>
                        <?php if( Auth::getUser()->isSuperUser() ): ?>
                            <td>
                                <?= $t->ee( $patchPanelPort->getPatchPanel()->getCabinet()->getName() ) ?>
                            </td>
                        <?php endif; ?>
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
            </tbody>
        </table>
    </div>
</div>

<?php if( Auth::getUser()->isSuperUser() ): ?>
    <?= $t->insert( 'customer/overview-tabs/cross-connect-history' ); ?>
<?php endif; ?>
