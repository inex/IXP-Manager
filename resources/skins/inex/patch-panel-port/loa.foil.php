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
                <td style="text-align: left; vertical-align: top; width="50%">
                    <img alt="[INEX Logo]" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAARMAAAA6CAIAAAAcHyqlAAAAAXNSR0IArs4c6QAA
        AAlwSFlzAAALEwAACxMBAJqcGAAAA6dpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAA
        ADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhN
        UCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3
        LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpE
        ZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6eG1wPSJo
        dHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIgogICAgICAgICAgICB4bWxuczp0
        aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgICAgICAgICAg
        eG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iPgogICAg
        ICAgICA8eG1wOk1vZGlmeURhdGU+MjAxNy0wMy0yNVQxMTowMzowNjwveG1wOk1v
        ZGlmeURhdGU+CiAgICAgICAgIDx4bXA6Q3JlYXRvclRvb2w+UGl4ZWxtYXRvciAz
        LjY8L3htcDpDcmVhdG9yVG9vbD4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+
        MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6Q29tcHJlc3Npb24+
        NTwvdGlmZjpDb21wcmVzc2lvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVu
        aXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPHRpZmY6WVJlc29s
        dXRpb24+NzI8L3RpZmY6WVJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOlhSZXNv
        bHV0aW9uPjcyPC90aWZmOlhSZXNvbHV0aW9uPgogICAgICAgICA8ZXhpZjpQaXhl
        bFhEaW1lbnNpb24+Mjc1PC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAg
        PGV4aWY6Q29sb3JTcGFjZT4xPC9leGlmOkNvbG9yU3BhY2U+CiAgICAgICAgIDxl
        eGlmOlBpeGVsWURpbWVuc2lvbj41ODwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAg
        ICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRh
        Pgqx2RGkAAAlQElEQVR4Ae1dB3wUxfe/vV7SLrn0CiEJPdTQpCNFRboCggKiqCCC
        IiL+bPxVUARF+QEWUEBRiigIIi3Si/QAIY2QQnpPLtdv7//dO7hcLrt7l0ry0+U+
        ZHZ25s3b2Xkzr80bwmQycf696tcDRpJUqdUcDlEDjEkiFvN5vBr59zKy8wrmvvvR
        6ctxUolk22cf9u0WzVSyofL1BoNGq62JKkFwpGIxl8tlaUil1hiMRgJFG+1yiIZO
        p9fqdTXxt2AEzFxk0npihyY0Oi1B0HQFiIVLEJYmiH8pp54dXVah3LRzz9krcUbS
        aAeK4BDdO7abM2WCp4e73SPL7dur13310y8mE4nh2DGqzbr3l7YND6Mt2SCZhcWl
        X275+WZyit1kiTEgFAiH9Y2ZPu4xkVBA29bx85e++2VvWUUFO3XR1nUyk0KDLxjS
        N+bpcY+JRcKatVIzstb9sD31bhYt9aK6SCAc8/CgyY+NqFnXyZyU9Mx1P+y4czeL
        9jVJkhQJRGOHD5owcijfSYj/FmPqgYMnzqza9INOb+CYyBpluKcuXfX3UcyY8HiN
        R1RGXmGRZRDjq99Iuv38Wx+sXrqwR+cOtIXrn/nb4dj123aZ4djRDgdT7OWbt3p3
        7dwxMrxmQ0ajcfmGTZfjk+jesWbxuucQBO/KraR+3aM7RNCgsW3vH1v3HjCRQN4e
        //tNEmeuxCnkHsP69bqfU4u/lWr1e2s2HD5zwUTiUzI0QXCvJyYP6tW9iSgH06qJ
        YzKQep1RpTNUavEzKvVGrcGkM5J6E4cEnlyCL+CL+IRIyJcKuVIx30UkkOEWH5VL
        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
        pEgAAAAASUVORK5CYII=" />
                </td>
                <td style="text-align: right; width="50%">
                    Internet&nbsp;Neutral&nbsp;Exchange&nbsp;Association<br>
                    Company Limited by Guarantee,<br>
                    4027 Kingswood Road,<br>
                    Citywest, Dublin<br>
                    D24 AX06 Ireland<br>
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
                The co-location provider must also advise us by email to when this new connection has been completed
                and at that time provide the co-location reference for the cross connect as well as any test results
                of the new circuit.
            </h4>
        </blockquote>

        <p>
            With this letter, INEX hereby authorises <?= $ppp->customer->name ?>
            and / or its agents to order a connection to the following demarcation point:
        </p>

        <p>
            <table border="0" width="100%" style="border: 2px solid #000000; padding: 5px;">
                <tr>
                    <td width="10%"></td>
                    <td>
                      <b>Location:</b>
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
                      <?= $ppp->name() ?>
                    </td>
                </tr>
            </table>
            <br>
        </p>
        <p>
            This authority is limited to the provisioning for the purpose of the initial installation, and will expire
            60 days from the date of issue (above left). This LoA does not oblige INEX to pay any fees or charges
            associated with such cross-connect services.
        </p>
        <p>
            The <?= config( 'ixp_fe.lang.customer.one' ) ?> agrees that should INEX issue a request to have this cross connect disconnected at any time,
            the <?= config( 'ixp_fe.lang.customer.one' ) ?> will arrange to have the cross connect decommissioned within 30 days and accept associated
            disconnection costs where applicable.
        </p>
        <p>
            Should you have any questions or concerns regarding this Letter of Authority, please contact our NOC
            via the details found at <a href="https://www.inex.ie/support/">https://www.inex.ie/support/</a>.
            <em>We generate our LoA's via our provisioning system. Each LoA can be individually
            authenticated by clicking on the following unique link:</em><br><br>
        &nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="<?= route ( 'patch-panel-port-loa@verify' , [ 'ppp' => $ppp->id , 'code' => $ppp->loa_code ] ) ?>">
              <?= route ( 'patch-panel-port-loa@verify' , [ 'ppp' => $ppp->id , 'code' => $ppp->loa_code ] ) ?>
            </a>
        </p>

        <p style="text-align: center; font-size: 0.8em">
            <br><br>
            <em>
                Internet Neutral Exchange Association Company Limited by Guarantee (INEX) is a company registered in Dublin, Ireland with the Companies Registration Office (#253804).
                Our registered office is 1-2 Marino Mart, Fairview, Dublin 3 but our correspondence and visiting address is as above.
                More details at: <a href="https://www.inex.ie/">www.inex.ie</a>. Contact details: <a href="https://www.inex.ie/support/">https://www.inex.ie/support/</a>
            </em>
        </p>
    </body>
</html>
