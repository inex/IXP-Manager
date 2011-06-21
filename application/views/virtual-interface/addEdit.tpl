{tmplinclude file="header.tpl"}

<script>
    YAHOO.namespace( 'IXP' );

    // View panel
    initViewPanel = function() {ldelim}
        var viewPanel = new YAHOO.widget.Panel( "viewPanel", {ldelim}
            close:       true,
            visible:     false,
            modal:       true,
            width:       '500px',
            fixedcenter: true
        {rdelim} );

        viewPanel.render();

        showViewPanel = function( o, p ) {ldelim}
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
                    "{genUrl}/virtual-interface/port-config/id/" + p.id,
                    callback,
                    null
            );

            viewPanel.show();

        {rdelim}
    {rdelim}

    YAHOO.util.Event.onDOMReady( initViewPanel );

</script>


<div class="content">

{if $isEdit}
    <h2>{$frontend.pageTitle} :: Editing</h2>
{else}
    <h2>Create New Customer Interface  </h2>
{/if}

{tmplinclude file="message.tpl"}


{$form}

{if $isEdit}

    <dl>
    <dt></dt>

    <dd>

    <fieldset>

        <legend>Physical Interfaces</legend>

        <div id="physicalInterfacesTable">

        <table id="myTable">

        <thead>
        <tr>
            <th>Location</th>
            <th>Switch</th>
            <th>Port</th>
            <th>Speed/Duplex</th>
            <th></th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        {foreach from=$phyInts item=int}

            <tr>
                <td>
                    {$int.Switchport.SwitchTable.Cabinet.Location.name}
                </td>
                <td>
                    {$int.Switchport.SwitchTable.name}
                </td>
                <td>
                    {$int.Switchport.name}
                </td>
                <td>
                    {$int.speed}/{$int.duplex}
                </td>
                <td>
                    <form action="{genUrl controller='physical-interface' action='edit' id=$int.id}" method="post">
                        <input type='hidden' name='return' value="{genUrl controller='virtual-interface' action='edit'}{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="edit" />
                    </form>
                </td>
                <td>
                    <form action="{genUrl controller='physical-interface' action='delete' id=$int.id}" method="post">
                        <input type='hidden' name='return' value="{genUrl controller='virtual-interface' action='edit'}{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="delete"
                            onClick="return confirm( 'Are you sure you want to delete this tuple?' );"
                        />
                    </form>
                </td>
            </tr>

        {/foreach}

        </tbody>

        </table>

        </div>

        <script>
        YAHOO.util.Event.addListener( window, "load", function() {ldelim}
            YAHOO.example.TableGenerator = new function() {ldelim}

                var myColumnDefs = [
                    {ldelim}key:"location",label:"Location",sortable:false{rdelim},
                    {ldelim}key:"switch",label:"Switch",sortable:false{rdelim},
                    {ldelim}key:"port",label:"Port",sortable:false{rdelim},
                    {ldelim}key:"speed-duplex",label:"Speed / Duplex",sortable:false{rdelim},
                    {ldelim}key:"blank1",label:"", sortable:false{rdelim},
                    {ldelim}key:"blank2",label:"", sortable:false{rdelim}
                ];

                this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get( "myTable" ) );
                this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
                this.myDataSource.responseSchema = {ldelim}
                    fields: [
                                {ldelim}key:"location"{rdelim},
                                {ldelim}key:"switch"{rdelim},
                                {ldelim}key:"port"{rdelim},
                                {ldelim}key:"speed-duplex"{rdelim},
                                {ldelim}key:"blank1"{rdelim},
                                {ldelim}key:"blank2"{rdelim}
                    ]
                {rdelim};

                var oConfigs = {ldelim}
                {rdelim};

                this.myDataTable = new YAHOO.widget.DataTable( "physicalInterfacesTable", myColumnDefs, this.myDataSource, oConfigs );

                // Enable row highlighting
                this.myDataTable.subscribe( "rowMouseoverEvent", this.myDataTable.onEventHighlightRow   );
                this.myDataTable.subscribe( "rowMouseoutEvent",  this.myDataTable.onEventUnhighlightRow );

            {rdelim};
        {rdelim});
        </script>


        <form action="{genUrl controller='physical-interface' action='add'}" method="post" style="text-align: right">
            <input type="submit" name="submit" class="button" value="Add New" />
            <input type='hidden' name='virtualinterfaceid' value='{$object.id}' />
            <input type='hidden' name='return' value="{'virtual-interface/edit/id/'|cat:$object.id}" />
        </form>


    </fieldset>

    </dd>
    </dl>





    <dl class="zend_form">
    <dt></dt>

    <dd>

    <fieldset>

        <legend>VLAN Interfaces</legend>

        <div id="vlanInterfacesTable">

        <table id="myVlanTable">

        <thead>
        <tr>
            <th>VLAN Name</th>
            <th>VLAN ID</th>
            <th>IPv4 Address</th>
            <th>IPv6 Address</th>
            <th></th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        {foreach from=$vlanInts item=int}

            <tr>
                <td>
                    {$int.Vlan.name}
                </td>
                <td>
                    {$int.Vlan.number}
                </td>
                <td>
                    {$int.Ipv4address.address}
                </td>
                <td>
                    {$int.Ipv6address.address}
                </td>
                <td>
                    <form action="{genUrl controller='vlan-interface' action='edit' id=$int->id}" method="post">
                        <input type='hidden' name='return' value="{genUrl controller='virtual-interface' action='edit'}{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="edit" />
                    </form>
                </td>
                <td>
                    <form action="{genUrl controller='vlan-interface' action='delete' id=$int->id}" method="post">
                        <input type='hidden' name='return' value="{genUrl controller='virtual-interface' action='edit'}{'/id/'|cat:$object.id}" />
                        <input type="submit" name="submit" class="button" value="delete"
                            onClick="return confirm( 'Are you sure you want to delete this tuple?' );"
                        />
                    </form>
                </td>
            </tr>

        {/foreach}

        </tbody>

        </table>

        </div>

        <script>
        YAHOO.util.Event.addListener( window, "load", function() {ldelim}
            YAHOO.example.TableGenerator = new function() {ldelim}

                var myColumnDefs = [
                    {ldelim}key:"name",label:"VLAN Name",sortable:false{rdelim},
                    {ldelim}key:"vlantag",label:"VLAN ID",sortable:false{rdelim},
                    {ldelim}key:"v4ip",label:"IPv4 Address",sortable:false{rdelim},
                    {ldelim}key:"v6ip",label:"IPv6 Address",sortable:false{rdelim},
                    {ldelim}key:"blank1",label:"", sortable:false{rdelim},
                    {ldelim}key:"blank2",label:"", sortable:false{rdelim}
                ];

                this.myDataSource = new YAHOO.util.DataSource(YAHOO.util.Dom.get( "myVlanTable" ) );
                this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
                this.myDataSource.responseSchema = {ldelim}
                    fields: [
                                {ldelim}key:"name"{rdelim},
                                {ldelim}key:"vlantag"{rdelim},
                                {ldelim}key:"v4ip"{rdelim},
                                {ldelim}key:"v6ip"{rdelim},
                                {ldelim}key:"blank1"{rdelim},
                                {ldelim}key:"blank2"{rdelim}
                    ]
                {rdelim};

                var oConfigs = {ldelim}
                {rdelim};

                this.myDataTable = new YAHOO.widget.DataTable( "vlanInterfacesTable", myColumnDefs, this.myDataSource, oConfigs );

                // Enable row highlighting
                this.myDataTable.subscribe( "rowMouseoverEvent", this.myDataTable.onEventHighlightRow   );
                this.myDataTable.subscribe( "rowMouseoutEvent",  this.myDataTable.onEventUnhighlightRow );

            {rdelim};
        {rdelim});
        </script>

		<table border="0" width="100%">
		<tr>
			<td width="100%"></td>
			<td style="text-align: right">
                <!-- <input id="viewPanel-portConfig-button" type="submit" name="portConfig" class="button" value="Port Configuration" />
                    <script>
                        YAHOO.namespace( 'IXP' );
                        YAHOO.util.Event.addListener(
                            document.getElementById( 'viewPanel-portConfig-button' ),
                            'click',
                            alert('123'), {ldelim}
                                    id: {$object.id}
                                {rdelim}
                            );
                    </script> -->
			</td>
			<td>
		        <form action="{genUrl controller='vlan-interface' action='add'}" method="post" style="text-align: right">
		            <input type="submit" name="submit" class="button" value="Add New" />
		            <input type='hidden' name='virtualinterfaceid' value='{$object.id}' />
		            <input type='hidden' name='return' value='virtual-interface/edit/id/{$object.id}' />
		        </form>
			</td>
		</tr>
		</table>
    </fieldset>

    </dd>
    </dl>

{/if}

</div>

{tmplinclude file="footer.tpl"}

