<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    Letter of Authority (LoA) - Verification System
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<?php if( !$t->ppp || $t->ppp->getLoaCode() != $t->loaCode ): ?>

    <div class="alert alert-danger">
        <strong>Verification Failed!</strong> We could not verify the LoA details you sent.
        <strong>Contact us before proceeding with this cross connect installation!</strong>
    </div>

<?php elseif( !$t->ppp->isStateAwaitingXConnect() ): ?>

    <div class="alert alert-warning">
        <strong>Verification Warning!</strong> This is a valid LoA <b>but</b> our system
        indicates that we are not expecting a cross connect for this port.
        <strong>Contact us before proceeding with this cross connect installation!</strong>
    </div>

<?php elseif( $t->ppp->getLoaCode() == $t->loaCode ): ?>

    <div class="alert alert-success">
        <strong>Verification Succeeded!</strong> This is a valid LoA for a new cross connect
        where our circuit ID is <?= $t->ee( $t->ppp->getCircuitReference() ) ?>. Please proceed with
        this cross connect installation with the following confirming details.
    </div>


    <div class="well">
        <table width="100%">
            <tr>
                <td width="10%"></td>
                <td><b>Facility:</b></td>
                <td><?= $t->ee( $t->ppp->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?></td>
            </tr>
            <tr>
                <td width="10%"></td>
                <td><b>Rack:</b></td>
                <td><?= $t->ee( $t->ppp->getPatchPanel()->getCabinet()->getColocation() ) ?></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Patch Panel:</b></td>
                <td><?= $t->ee( $t->ppp->getPatchPanel()->getColoReference() ) ?></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Type:</b></td>
                <td><?= $t->ee( $ppp->getPatchPanel()->resolveCableType() ) ?> / <?= $t->ee( $ppp->getPatchPanel()->resolveConnectorType() ) ?></td>
            </tr>
            <tr>
                <td></td>
                <td><b>Port:</b></td>
                <td><?= $t->ee( $t->ppp->getName() ) ?></td>
            </tr>
        </table>
    </div>

    <blockquote>
        <h4>
            Prior to connecting to our demarcation as described below, the co-location provider must ensure that
            this link does not terminate on any active ports. If it does, please contact our NOC immediately.
            The co-location provider must also advise us by email to when this new connection has been completed
            and at that time provide the co-location reference for the cross connect as well as any test results
            of the new circuit.
        </h4>
    </blockquote>


<?php else: ?>

    <div class="alert alert-warning">
        <strong>Verification Warning!</strong>
        <strong>Contact us before proceeding with this cross connect installation!</strong>
    </div>

<?php endif; ?>


<div class="well">
    <h4>Contact Details</h4>

    <p>
        If you need to contact us, please use the <em>Support</em> link above or email us on
        <?= env( 'IDENTITY_SUPPORT_EMAIL' ) ?> or call us on <?= env( 'IDENTITY_SUPPORT_PHONE' ) ?>.
    </p>
</div>


<?php $this->append() ?>