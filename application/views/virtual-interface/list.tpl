{tmplinclude file="header.tpl"}


<script>
    YAHOO.namespace( 'IXP' );

    // View panel
    YAHOO.IXP.initViewPanel = function() {ldelim}
        var viewPanel = new YAHOO.widget.Panel( "viewPanel", {ldelim}
            close:       true,
            visible:     false,
            modal:       true,
            width:       '500px',
            fixedcenter: true
        {rdelim} );

        viewPanel.render();

        YAHOO.IXP.showViewPanel = function( o, p ) {ldelim}
            YAHOO.util.Dom.setStyle( 'viewPanel', 'display', 'block' );

            // load the appropriate view content

            var ajaxSuccess = function( o ) {ldelim}

                YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = o.responseText;

                YAHOO.util.Event.addListener(
                    'view-container-close',
                    'click',
                    function() {ldelim}
                        viewPanel.hide();
                        YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = '';
                    {rdelim}
                );

            {rdelim}

            var ajaxFailure = function( o ) {ldelim}

                YAHOO.util.Dom.get( 'viewPanel' ).innerHTML = " \
                    <div class=\"hd\">AJAX Error</div>\
                    <div class=\"bd\">\
                        <p>Error executing AJAX request:</p>\
                        <p>" + o.status + ": " + o.statusText + "\
                    </div>\
                    <div class=\"ft\">AJAX Error</div>";

            {rdelim}

            var callback = {ldelim}
                success: ajaxSuccess,
                failure: ajaxFailure
            {rdelim};

            var ajaxRequest = YAHOO.util.Connect.asyncRequest(
                    "GET",
                    "{genUrl}/" + p.controller + "/view/id/" + p.id + "/perspective/panel",
                    callback,
                    null
            );

            viewPanel.show();

        {rdelim}
    {rdelim}

    YAHOO.util.Event.onDOMReady( YAHOO.IXP.initViewPanel );

</script>

<div class="yui-g" style="height: 600px">

<script type="text/javascript" src="/ixp/js/virtual-interface-list.js"></script>

<table class="adminheading" border="0">
<tr>
    <th class="Interface">
        {$frontend.pageTitle}
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}


{literal}
<style>
#autocomplete, #autocomplete_sn {
    height: 25px;
}
#dt_input {
    width: 150px;
}
#dt_input_sn {
    width: 100px;
}

/* This hides the autocomplete drop downs */
#dt_ac_container, #dt_ac_sn_container {
    display: none;
}

#dataTable {
    text-align: center;
    padding-top: 20px;
}

#dataTable table {
    margin-left:auto;
    margin-right:auto;
}

#dataTable, #dataTable .yui-dt-loading {
    text-align: center;
    background-color: transparent;
}

</style>
{/literal}

<table width="900" id="centre">
<tr>
    <td>
        <div id="autocomplete">
            <label for="dt_input">Member Name: </label><input id="dt_input" type="text" value="">
            <div id="dt_ac_container"></div>
        </div>
    </td>
    <td>
        <div id="autocomplete_sn">
            <label for="dt_input_sn">Shortname: </label><input id="dt_input_sn" type="text" value="">
            <div id="dt_ac_sn_container"></div>
        </div>
    </td>
    <td>
    	<table>
    	<tr>
    		<td>
                <form action="{genUrl controller=$controller action='add'}" method="post">
                    <input type="submit" name="submit" class="button" value="Add New" />
                </form>
        	</td>
        	<td>
				<form action="{genUrl controller='vlan-interface' action='quick-add'}" method="post">
                    <input type="submit" name="submit" class="button" value="Quick Add" />
                </form>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>

<p>
<br /><br />
</p>

<div id="dataTable" style="margin-top: 70px;">
</div>

<div id="viewPanel" class="viewPanel">
    <div class="hd" id="viewPanelHeader"></div>
    <div class="bd" id="viewPanelBody">Loading...</div>
    <div class="ft" id="viewPanelFooter"></div>
</div>


</div>


{tmplinclude file="footer.tpl"}
