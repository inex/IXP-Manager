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
        <li><a href="#rejected" data-toggle="tab">Rejected / Ignored Peers</a></li>
</ul>

<div class="tab-content" style="min-height: 400px;">

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
        {include file="peering-manager/index-rejected.tpl"}
    </div>

</div>

<div class="modal hide" id="modal-peering-notes" style="left: 45%; width: 760px;">
    <div class="modal-header" id="modal-peering-notes-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3 id="modal-peering-notes-header-h3">Peering Notes</h3>
    </div>
    <div class="modal-body" id="modal-peering-notes-body" style="max-height: 600px;">
        <div id="peering-notes-container">
        <form id="peering-notes-form" class="form-horizontal" action="" horizontal="1" method="post" accept-charset="UTF-8" enctype="application/x-www-form-urlencoded" name="peering-notes-form">
            <textarea class="span7 mono disabled" rows="12" cols="80" id="modal-peering-notes-message" name="message">Please wait... loading...</textarea>
            <input type="hidden" id="modal-peering-notes-custid" name="custid" value="" />
        </form>
        </div>
    </div>
    <div class="modal-footer" id="modal-peering-notes-footer">
        <button id="modal-peering-notes-footer-close" class="btn btn-danger">Cancel</button>
        <button id="modal-peering-notes-footer-save" class="btn btn-success">Save</button>
    </div>
</div>

<div class="modal hide" id="modal-peering-request" style="left: 45%; width: 760px;">
    <div class="modal-header" id="modal-peering-request-header">
        <button class="close" data-dismiss="modal">×</button>
        <h3>Send Peering Request by Email</h3>
    </div>
    <div class="modal-body" id="modal-peering-request-body" style="max-height: 600px;">
        <p>Please wait... loading...</p>
    </div>
    <div class="modal-footer" id="modal-peering-request-footer">
        <button id="modal-peering-request-footer-close" class="btn">Cancel</button>
        <button id="modal-peering-request-footer-marksent" rel="tooltip"
            title="Don't send this email but mark it as sent - useful if you are sending requests manually but want to track them here." class="btn btn-primary">Mark Sent</button>
        <button id="modal-peering-request-footer-sendtome" rel="tooltip"
            title="Just send this email to me so I can see how it looks." class="btn btn-primary">Send to Me</button>
        <button id="modal-peering-request-footer-send" class="btn btn-danger">Send</button>
    </div>
</div>

<script type="text/javascript">
{include file="peering-manager/index.js"}
</script>
    
{include file="footer.tpl"}
