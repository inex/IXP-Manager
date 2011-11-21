{tmplinclude file="header.tpl" pageTitle="IXP Manager :: SEC Event Notification Config"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        Member Information :: SEC Event Logs
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>


<h2>About SEC Events</h2>

<p>
At INEX, we use the <a href="http://simple-evcorr.sourceforge.net/">SEC - simple event correlator</a> to
monitor various logs and feed information that we feel is important into a processor within the IXP Manager.
The processor parses the entry and then correlates that information with our IXP database to match it
to switch ports and INEX members. See more information <a href="{genUrl controller='dashboard'
action='sec-event-email-config'}">here</a>.
</p>

<p><br /></p>

<table id="secEventTable" class="display" cellspacing="0" cellpadding="0" border="0" style="display: none;">
	<thead>
	<tr>
        <th>Date</th>
	    <th>Log Message</th>
	</tr>
	</thead>

    <tbody>

    {foreach from=$logs item=log}
        <tr>
            <td>{$log.recorded_date|date_format:'%Y-%m-%d %H:%M:%S'}</td>
            <td>{$log.message}</td>
        </tr>
    {/foreach}

    </tbody>
</table>



</div>

</div>


<script type="text/javascript">
{literal}
	$('#secEventTable').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"iDisplayLength": 25,
	}).show();
{/literal}
</script>

{tmplinclude file="footer.tpl"}

