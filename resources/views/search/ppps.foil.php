<?php
    /** @var object $t */
    /** @var \IXP\Models\PatchPanelPort[] $ppps */
    $ppps = $t->results;
?>

<table class="table table-striped w-100">
    <thead class="thead-dark">
        <tr>
            <th>
                Facility
            </th>
            <th>
                Cabinet
            </th>
            <th>
                Patch Panel
            </th>
            <th>
                Port
            </th>
            <th>
                <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
            </th>
            <th>
                Colo Ref
            </th>
            <th>
                Colo Billing Ref
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $ppps as $ppp ): ?>
            <tr>
                <td>
                    <?= $t->ee( $ppp->patchPanel->cabinet->location->name ) ?>
                </td>
                <td>
                    <?= $t->ee( $ppp->patchPanel->cabinet->name ) ?>
                </td>
                <td>
                    <a href="<?= route( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $ppp->patch_panel_id ] ) ?>">
                        <?= $t->ee( $ppp->patchPanel->name ) ?>
                    </a>
                </td>
                <td>
                    <a href="<?= route( 'patch-panel-port@view', [ 'ppp' => $ppp->id ] ) ?>">
                        <?= $t->ee( $ppp->name() ) ?>
                    </a>
                </td>
                <td>
                    <?php if( $ppp->customer ): ?>
                        <?= $ppp->customer->getFormattedName() ?>
                    <?php endif; ?>
                </td>
                <td>
                    <code><?= $ppp->colo_circuit_ref ?></code>
                </td>
                <td>
                    <code><?= $ppp->colo_billing_ref ?></code>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>