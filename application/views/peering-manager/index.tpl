{include file="header.tpl" pageTitle="IXP Manager :: Peering Manager"}

<div class="page-content">

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<ul class="nav nav-tabs">
    <li class="active">
        <a href="#potential" data-toggle="tab">Potential Peers</a></li>
        <li><a href="#potential-bilat" data-toggle="tab">Potential Bilateral</a></li>
        <li><a href="#peers" data-toggle="tab">Peers</a></li>
        <li><a href="#rejected" data-toggle="tab">Rejected</a></li>
</ul>

<div class="tab-content">

    <div class="tab-pane active" id="potential">
        {include file="peering-manager/index-potential.tpl"}
    </div>

    <div class="tab-pane" id="potential-bilat">
        {include file="peering-manager/index-potential-bilateral.tpl"}
    </div>

    <div class="tab-pane" id="peers">
        {include file="peering-manager/index-peers.tpl"}
    </div>
    
    <div class="tab-pane" id="rejected">
    </div>

</div>

    
{include file="footer.tpl"}
