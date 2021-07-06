<div class="card mb-4">
    <div class="card-header d-flex">
        <h3>
            Historical Cross Connects
        </h3>
    </div>
    <div class="card-body">
        <table class="table table-striped table-responsive-ixp collapse w-100">
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
                <?php foreach( $t->crossConnectsHistory as $hist ):
                    /** @var $hist \IXP\Models\PatchPanelPortHistory */
                    $ppp  = $hist->patchPanelPort;
                    $pp   = $ppp->patchPanel;
                    ?>
                    <tr>
                        <td>
                            <a href="<?= route( "patch-panel-port@view" , [ "ppp" => $hist->patch_panel_port_id ] ) ?>">
                                <?= $t->ee( $pp->name ) ?> ::
                                <?= $t->ee( $ppp->name() ) ?>
                            </a>
                        </td>

                        <td>
                            <?= $t->ee( $hist->colo_circuit_ref ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $pp->cabinet->location->name ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $pp->cabinet->name ) ?>
                        </td>
                        <td>
                            <?= $hist->assigned_at ?>
                        </td>
                        <td>
                            <?= $hist->ceased_at ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>