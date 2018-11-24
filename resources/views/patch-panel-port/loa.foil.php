<html>
    <head>
        <title>
            LoA - <?= $t->ppp->getCircuitReference() ?> - <?= date('Y-m-d' ) ?>
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
                    <?= date( "F d, Y" ) ?>
                </td>
            </tr>
        </table>

        <h2>
            Letter of Authority (LoA) - Our Reference:</b> <?= $t->ppp->getCircuitReference() ?>
        </h2>

        <hr>

        <blockquote>
            <h4>
                Prior to connecting to our demarcation as described below, the co-location provider must ensure that
                this link does not terminate on any active ports. If it does, please contact our NOC immediately.
                The co-location provider must also advise us by email to when this new connection has been completed
                and at that time provide the co-location reference for the cross connect as well as any test results
                of the new circuit.
            </h4>
        </blockquote>

        <p>
            To whom it may concern,
        </p>

        <p>
            With this letter, <?= env( 'IDENTITY_LEGALNAME' ) ?> hereby authorises <?= $t->ppp->getCustomer()->getName() ?>
            and / or its agents to order a connection to the following demarcation point:
        </p>

        <p>
            <table border="0" width="100%" style="border: 2px solid #000000; padding: 10px;">
                <tr>
                    <td width="10%"></td>
                    <td><b>Facility:</b></td>
                    <td><?= $t->ee( $t->ppp->getPatchPanel()->getCabinet()->getLocation()->getName() ) ?></td>
                </tr>
                <tr>
                    <td width="10%"></td>
                    <td><b>Rack:</b></td>
                    <td><?= $t->ee( $t->ppp->getPatchPanel()->getCabinet()->getCololocation() ) ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>Patch Panel:</b></td>
                    <td><?= $t->ee( $t->ppp->getPatchPanel()->getColoReference() ) ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>Type:</b></td>
                    <td><?= $t->ee( $t->ppp->getPatchPanel()->resolveCableType() ) ?> / <?= $t->ee( $t->ppp->getPatchPanel()->resolveConnectorType() ) ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>Port:</b></td>
                    <td>
                        <?= $t->ee( $t->ppp->getName() ) ?> <?php if( $t->ppp->hasSlavePort() ){ ?><em>(duplex port)</em><?php } ?>
                    </td>
                </tr>
            </table>
            <br>
        </p>

        <p>
            This authority is limited to the provisioning for the purpose of the initial installation, and will expire
            60 days from the date of issue (above left). This LoA does not obligate
            <?= env( 'IDENTITY_LEGALNAME' ) ?>  to pay any fees or charges associated with such cross-connect services.
        </p>

        <p>
            The customer agrees that should the applicable service to which this LoA was issued be requested
            to be cancelled at any time during service, the customer must prior to contract cease date arrange
            to have associated cross connects decommissioned from the equipment / ports and accept associated
            disconnect costs where applicable.
        </p>

        <p>
            Should you have any questions or concerns regarding this Letter of Authority, please contact our NOC
            via the details below. <em>We generate our LoA's via our provisioning system. Each LoA can be individually
            authenticated by clicking on the following unique link:</em><br><br>
            &nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="<?= route ( 'patch-panel-port@verify-loa' , [ 'id' => $t->ppp->getId() , 'loa' => $t->ppp->getLoaCode() ] ) ?>"
                ><?= route ( 'patch-panel-port@verify-loa' , [ 'id' => $t->ppp->getId() , 'loa' => $t->ppp->getLoaCode() ] ) ?></a>
        </p>


        <p>
            IXP user: skin this template and insert your company legal information and contact details.
        </p>
    </body>
</html>
