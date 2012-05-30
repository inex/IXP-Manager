{include file="header.tpl" pageTitle="IXP Manager :: Peering Manager"}

<style>
    body{ overflow-y: scroll; }
</style>

<div class="page-content">

{include file="message.tpl"}
<div id='ajaxMessage'></div>

<ul class="nav nav-tabs">
    <li class="active">
        <a href="#potential" data-toggle="tab">Potential Peers</a></li>
        <li><a href="#potential-bilat" data-toggle="tab">Potential Bilateral Peers</a></li>
        <li><a href="#peers" data-toggle="tab">Peers</a></li>
        <!--  li><a href="#rejected" data-toggle="tab">Rejected Peers</a></li -->
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
    
    <!-- div class="tab-pane" id="rejected">
    </div -->

</div>


<div class="modal hide" id="modal-peering-request">
    <div class="modal-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3>Send Peering Request by Email</h3>
    </div>
    <div class="modal-body">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn">Close</a>
        <a href="#" class="btn btn-primary">Send</a>
    </div>
</div>

<script type="text/javascript">
{include file="peering-manager/index.js"}
</script>
    
{include file="footer.tpl"}
