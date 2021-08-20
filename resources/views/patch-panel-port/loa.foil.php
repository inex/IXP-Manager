<?php
    $ppp = $t->ppp; /** @var $ppp \IXP\Models\PatchPanelPort*/
?>
<html>
    <head>
        <title>
            LoA - <?= $ppp->circuitReference() ?> - <?= now()->format('Y-m-d' ) ?>
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
    <body>
        <table width="100%" border="0">
            <tr>
                <td style="text-align: left; vertical-align: top;">
                    [Logo]
                </td>
                <td style="text-align: right">
                    Example IXP<br>
                    12 Some Street<br>
                    A Town<br>
                    PostCode, Country<br>
                    <br>
                    <?= now()->format( "F d, Y" ) ?>
                </td>
            </tr>
        </table>

        <h2>
            Letter of Authority (LoA) - Our Reference:</b> <?= $ppp->circuitReference() ?>
        </h2>
        <hr>
        <blockquote>
            <h4>
                Prior to connecting to our demarcation as described below, the co-location provider must ensure that
                this link does not terminate on any active ports. If it does, please contact our NOC immediately.
                The co-location provider must also advise us by email when this new connection has been completed
                and at that time provide the co-location reference for the cross connect as well as any test results
                of the new circuit.
            </h4>
        </blockquote>
        <p>
            To whom it may concern,
        </p>
        <p>
            With this letter, <?= env( 'IDENTITY_LEGALNAME' ) ?> hereby authorises <?= $ppp->customer->name ?>
            and / or its agents to order a connection to the following demarcation point:
        </p>
        <p>
            <table border="0" width="100%" style="border: 2px solid #000000; padding: 10px;">
                <tr>
                    <td width="10%"></td>
                    <td>
                      <b>Facility:</b>
                    </td>
                    <td>
                        <?= $t->ee( $ppp->patchPanel->cabinet->location->name ) ?>
                    </td>
                </tr>
                <tr>
                    <td width="10%"></td>
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
            <br>
        </p>
        <p>
            This authority is limited to the provisioning for the purpose of the initial installation, and will expire
            60 days from the date of issue (above left). This LoA does not oblige
            <?= env( 'IDENTITY_LEGALNAME' ) ?>  to pay any fees or charges associated with such cross-connect services.
        </p>
        <p>
            The <?= config( 'ixp_fe.lang.customer.one' ) ?> agrees that should the applicable service to which this LoA was issued be requested
            to be cancelled at any time during service, the <?= config( 'ixp_fe.lang.customer.one' ) ?> must prior to contract cease date arrange
            to have the associated cross connects decommissioned from the equipment / ports and accept any associated
            disconnection costs where applicable.
        </p>
        <p>
            Should you have any questions or concerns regarding this Letter of Authority, please contact our NOC
            via the details below. <em>We generate our LoA's via our provisioning system. Each LoA can be individually
            authenticated by clicking on the following unique link:</em><br><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="<?= route ( 'patch-panel-port-loa@verify' , [ 'ppp' => $ppp->id , 'code' => $ppp->loa_code ] ) ?>"
                ><?= route ( 'patch-panel-port-loa@verify' , [ 'ppp' => $ppp->id , 'code' => $ppp->loa_code ] ) ?></a>
        </p>
        <p>
            IXP user: skin this template and insert your company legal information and contact details.
        </p>
    </body>
</html>