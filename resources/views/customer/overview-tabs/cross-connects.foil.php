<div class="card mb-4">
    <div class="card-header d-flex">
        <h3>
            Cross Connect
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
                    <?php if( $t->isSuperUser ): ?>
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
                    <?php if( $t->isSuperUser ): ?>
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
                <?php foreach( $t->crossConnects as $ppp ):
                    /** @var $ppp \IXP\Models\PatchPanelPort */
                    $pp = $ppp->patchPanel;
                    ?>
                    <tr>
                        <td>
                            <a href="<?= route( "patch-panel-port@view" , [ "ppp" => $ppp->id ] ) ?>">
                                <?= $t->ee( $pp->name ) ?>
                                <?= $t->ee( $ppp->name() ) ?>
                            </a>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->colo_circuit_ref ) ?>
                        </td>
                        <?php if( $t->isSuperUser ): ?>
                            <td>
                                <?= $t->ee( $ppp->ticket_ref ) ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <span class="badge badge-<?=$ppp->stateCssClass( $ppp->state, $t->isSuperUser ) ?>">
                            <?= $ppp->states() ?>
                            </span>
                        </td>
                        <td>
                            <?= $t->ee( $pp->cabinet->location->name ) ?>
                        </td>
                        <?php if( $t->isSuperUser ): ?>
                            <td>
                                <?= $t->ee( $pp->cabinet->name ) ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?= $ppp->assigned_at ?>
                        </td>
                        <td>
                            <?= $ppp->chargeable() ?>
                        </td>
                        <td>
                            <?= $ppp->ownedBy() ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if( $t->isSuperUser ): ?>
    <?= $t->insert( 'customer/overview-tabs/cross-connect-history' ); ?>
<?php endif; ?>
