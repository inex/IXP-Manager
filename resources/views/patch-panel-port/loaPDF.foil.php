<head>
    <title>
        Loa - <?= $t->ppp->getCustomer()->getName() ?> - <?= $t->ppp->getPatchPanel()->getCabinet()->getLocation()->getName() ?> - <?= $t->ppp->getPatchPanel()->getColoReference() ?> - <?= $t->ppp->getName()  ?>
    </title>
</head>
<table width="100%">
    <tr>
        <td style="text-align: left">
            Issue Date : <?= date( "F d, Y" ) ?>
        </td>
        <td style="text-align: right">
            Example IXP<br/>
            12 Some Street<br/>
            A Town<br/>
            PostCode, Country<br/>
        </td>
    </tr>
</table>
<p>
    circuit reference <?= sprintf( "%04d", $t->ppp->getId() ) ?> (*)
</p>

<h1>
    Letter of Agency (LoA)
</h1>
<hr>
<h3>
    Prior to connecting to our demarcation as described below, the co-location provider must ensure that this link does not terminate on any active ports.
    If it does, please contact our NOC immediately.
    The co-location provider must also advise us by email to when this new connection has been completed and at that time provide the co-location reference for the cross connect as well as any test results of the new circuit.
</h3>
<p>
    To whom it may concern,
</p>
<p>
    With this letter, ( hereby authorises <?= $t->ppp->getCustomer()->getName() ?> and /or its agents to order a connection to the following demarcation point:
</p>

<p>
    Location: <?= $t->ppp->getPatchPanel()->getCabinet()->getLocation()->getName() ?><br/>
    Panel: <?= $t->ppp->getPatchPanel()->getColoReference() ?><br/>
    Ports: <?= $t->ppp->getName()  ?>
</p>

<p>
    This authority is limited to the provisioning for the purpose of the initial installation, and will expire in 60 days from Date of Issue.
</p>

<p>
    This LOA does not obligate to pay any fees or charges associated with such cross-connect services.
</p>

<p>
    The Customer agrees that should the applicable service to which this LoA was issued be requested to cancel at any time during service,
    customer must prior to contract cease date arrange to have associated cross connects decommissioned from the equipment / ports and accept associated disconnect costs where applicable.
</p>

<p>
    Should you have any questions or concerns regarding this Letter of Agency, please contact our NOC via .
</p>

<p>
    This LoA can be authenticated via the following URL:<br/>
    <a target="_blank" href="<?= url( '/verify-loa' ).'/'.$t->ppp->getId().'/'.$t->ppp->getLoaCode()?>">
        http://ixp.dev/verify-loa/<?= $t->ppp->getId()?>/<?= $t->ppp->getLoaCode()?>
    </a>
</p>

<p>
    (*) circuit reference (<?=sprintf( "%04d", $t->ppp->getId() )?>)
</p>
<br/>

<p>
    Company legal Informations
</p>