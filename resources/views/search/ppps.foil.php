<?php
    /** @var object $t */
    /** @var Entities\PatchPanelPort[] $ppps */
    $ppps = $t->results;

?>


<table class="table table-striped" width="100%">
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
                    <?= $t->ee( $ppp->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?>
                </td>
                <td>
                    <?= $t->ee( $ppp->getPatchPanel()->getCabinet()->getName() ) ?>
                </td>
                <td>
                    <a href="<?= route( 'patch-panel-port/list/patch-panel', [ 'ppid' => $ppp->getPatchPanel()->getId() ] ) ?>">
                        <?= $t->ee( $ppp->getPatchPanel()->getName() ) ?>
                    </a>
                </td>
                <td>
                    <a href="<?= route( 'patch-panel-port@view', [ 'id' => $ppp->getId() ] ) ?>">
                        <?= $t->ee( $ppp->getName() ) ?>
                    </a>
                </td>
                <td>
                    <?php if( $ppp->getCustomer() ): ?>
                        <?= $ppp->getCustomer()->getFormattedName() ?>
                    <?php endif; ?>
                </td>
                <td>
                    <code><?= $ppp->getColoCircuitRef() ?></code>
                </td>
                <td>
                    <code><?= $ppp->getColoBillingRef() ?></code>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>

</table>


