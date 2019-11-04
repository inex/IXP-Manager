<div class="card mb-4">
    <div class="card-header d-flex">
        <h3>
            Historical Cross Connects
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
                <th>
                    Location
                </th>
                <th>
                    Cabinet
                </th>
                <th>
                    Assigned At
                </th>
                <th>
                    Disconnected At
                </th>
            </tr>
            </thead>
            <tbody>
                <?php foreach( $t->crossConnectsHistory as $ppph ): ?>
                    <tr>
                        <td>
                            <a href="<?= route( "patch-panel-port@view" , [ "id" => $ppph->getPatchPanelPort()->getId() ] ) ?>">
                                <?= $t->ee($ppph->getPatchPanelPort()->getPatchPanel()->getName() ) ?>
                                <?= $t->ee( $ppph->getPatchPanelPort()->getName() ) ?>
                            </a>
                        </td>

                        <td>
                            <?= $t->ee( $ppph->getColoCircuitRef() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ppph->getPatchPanelPort()->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $ppph->getPatchPanelPort()->getPatchPanel()->getCabinet()->getName() ) ?>
                        </td>
                        <td>
                            <?= $ppph->getAssignedAtFormated() ?>
                        </td>
                        <td>
                            <?= $ppph->getCeasedAt()->format( "Y-m-d" ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
