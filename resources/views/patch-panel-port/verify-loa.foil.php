<?php $this->layout( 'layouts/ixpv4' );
    /** @var object $t */
    $ppp = $t->ppp; /** @var $ppp \IXP\Models\PatchPanelPort */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Letter of Authority (LoA) - Verification System
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <?php if( $ppp->loa_code !== $t->loaCode ): ?>
        <div class="alert alert-danger" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-exclamation-triangle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    <b>Verification Failed!</b> We could not verify the LoA details you sent.
                    <b>Contact us before proceeding with this cross connect installation!</b>
                </div>
            </div>
        </div>
    <?php elseif( $ppp->loa_code === $t->loaCode ): ?>
        <div class="alert alert-success" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center">
                    <i class="fa fa-check-circle fa-2x"></i>
                </div>
                <div class="col-sm-12">
                    <b>Verification Succeeded!</b> This is a valid LoA for a new cross connect
                    where our circuit ID is <?= $t->ee( $ppp->circuitReference() ) ?>.
                    Please proceed with this cross connect installation with the following details.
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                Details
            </div>
            <div class="card-body">
                <table class="w-100">
                    <tr>
                        <td class="w-10"></td>
                        <td>
                          <b>Facility:</b>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->patchPanel->cabinet->location->name ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-10"></td>
                        <td>
                          <b>Rack:</b>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->patchPanel->cabinet->colocation ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                          <b>Patch Panel:</b>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->patchPanel->colo_reference ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                          <b>Type:</b>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->patchPanel->cableType() ) ?> / <?= $t->ee( $ppp->patchPanel->connectorType() ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                          <b>Port:</b>
                        </td>
                        <td>
                            <?= $t->ee( $ppp->name() ) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <blockquote class="blockquote border-left pl-2 m-4" style="border-width : 4px!important; border-color : #f8f9fa" >
            <h5>
                Prior to connecting to our demarcation as described below, the co-location provider must ensure that
                this link does not terminate on any active ports. If it does, please contact our NOC immediately.
                The co-location provider must also advise us by email to when this new connection has been completed
                and at that time provide the co-location reference for the cross connect as well as any test results
                of the new circuit.
            </h5>
        </blockquote>
    <?php else: ?>
        <div class="alert alert-warning">
            <b>Verification Warning!</b>
            <b>Contact us before proceeding with this cross connect installation!</b>
        </div>
    <?php endif; ?>

    <div class="card bg-light mt-4">
        <div class="card-body">
            <h5>Contact Details</h5>
            If you need to contact us, please use the <em>Support</em> link above or email us on
            <?= env( 'IDENTITY_SUPPORT_EMAIL' ) ?> or call us on <?= env( 'IDENTITY_SUPPORT_PHONE' ) ?>.
        </div>
    </div>
<?php $this->append() ?>