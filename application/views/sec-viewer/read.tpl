{include file="header.tpl" pageTitle="IXP Manager :: SEC Event Notification Config"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        Member Information :: SEC Event Logs
    </th>
</tr>
</table>

{include file="message.tpl"}

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

<table align="center">

<tr>
<td align="center">
    {genDoctrinePagerLinks controller='sec-viewer' action='read' pager=$pager}
</td>
</tr>

<tr>
<td align="center">
<div id="secEventTableContainer">
	<table id="secEventTable">
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
</td>
</tr>

<tr>
<td align="center">
    {genDoctrinePagerLinks controller='sec-viewer' action='read' pager=$pager showPageCount=1}
</td>
</tr>

</table>


</div>

</div>


<script type="text/javascript">
{literal}
    var secEventSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "secEventTable" ) );
    secEventSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

    secEventSource.responseSchema = {
        fields: [
            {key:'Date/Time'},
            {key:'Message'}
        ]
    };

    var secEventColumnDefs = [
        {key:'Date/Time'},
        {key:'Message', minWidth:700}
    ];

    var secEventDataTable = new YAHOO.widget.DataTable( "secEventTableContainer",
    	    secEventColumnDefs, secEventSource
    );
{/literal}
</script>

{include file="footer.tpl"}

