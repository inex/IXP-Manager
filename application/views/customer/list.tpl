{include file="header.tpl"}

<div class="yui-g" style="height: 600px; margin-bottom: 150px;">



<table class="adminheading" border="0">
<tr>
    <th class="Customer">
        IXP Members
    </th>
</tr>
</table>


{include file="message.tpl"}


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

<table width="700" id="centre">
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
        <form action="{genUrl controller=$controller action='add'}" method="post">
            <input type="submit" name="submit" class="button" value="Add New" />
        </form>
    </td>
</tr>
</table>

<p>
<br /><br />
</p>

<div id="dataTable" style="margin-top: 70px;">
</div>

</div>



{literal}
<script type="text/javascript">
(function() {
    var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    queryString = '/output/json/',
    myDataSource = null,
    myDataTable = null;


    var getCustData = function( query ) {
        myDataSource.sendRequest(
            'shortname/' + YAHOO.util.Dom.get( 'dt_input_sn' ).value
                + '/member/' + YAHOO.util.Dom.get( 'dt_input'    ).value
                + queryString,
            myDataTable.onDataReturnInitializeTable, myDataTable );
    };


    Event.onDOMReady(function() {

        var oACDS = new YAHOO.util.FunctionDataSource( getCustData );
        oACDS.queryMatchContains = true;
        var oAutoComp = new YAHOO.widget.AutoComplete( "dt_input", "dataTable", oACDS);
        oAutoComp.minQueryLength = 0;

        var oACDSShortName = new YAHOO.util.FunctionDataSource( getCustData );
        oACDSShortName.queryMatchContains = true;
        var oAutoCompShortName = new YAHOO.widget.AutoComplete( "dt_input_sn", "dataTable", oACDSShortName );
        oAutoCompShortName.minQueryLength = 0;


        var myColumnDefs = [
            { key:"id",
                label:"ID",
                sortable:true
            },
            { key:"member",
                label:"Member",
                sortable:true
            },
            { key:"shortname",
                label:"Short Name",
                sortable:true
            },
            { key:"autsys",
                label:"AS",
                abbr:"Autonomous System Number",
                sortable:true
            },
            { key:"peeringemail",
                label:"Peering Email",
                sortable:false
            },
            { key:"nocphone",
                label:"NOC Phone",
                sortable:false
            }
        ];

        myDataSource = new YAHOO.util.DataSource( "{/literal}{genUrl controller="customer" action="get-data"}{literal}/" );
        myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
        myDataSource.connXhrMode = "queueRequests";

        myDataSource.responseSchema = {
            resultsList: "ResultSet.Result",
            fields: [
                { key:"id",
                    parser:"number"
                },
                { key:"member",
                    parser:"string"
                },
                { key:"shortname",
                    parser:"string"
                },
                { key:"autsys",
                    parser:"number"
                },
                { key:"peeringemail"},
                {key:"nocphone"}
            ]
        };

        oConfigs = {
                paginator: new YAHOO.widget.Paginator({
                    rowsPerPage: 20
                }),
                initialRequest: "output/json/",
                sortedBy: { key: "shortname", dir:YAHOO.widget.DataTable.CLASS_ASC }
        };


        myDataTable = new YAHOO.widget.DataTable( "dataTable", myColumnDefs,
                myDataSource, oConfigs );

        // Enables row highlighting
        myDataTable.subscribe( "rowMouseoverEvent", myDataTable.onEventHighlightRow   );
        myDataTable.subscribe( "rowMouseoutEvent",  myDataTable.onEventUnhighlightRow );

        // Enable row selection
        myDataTable.set( "selectionMode", "single" );
        myDataTable.subscribe( "rowClickEvent", function ( oArgs ) {
                var elTarget = oArgs.target;
                var oRecord = this.getRecord( elTarget );
                window.location.assign( "{/literal}{genUrl controller="customer" action="view"}{literal}/id/"  + oRecord.getData( 'id' ) );
            }
        );



        var onContextMenuClick = function( p_sType, p_aArgs, p_myDataTable ) {
            var task = p_aArgs[1];
            if( task ) {
                // Extract which TR element triggered the context menu
                var elRow = this.contextEventTarget;
                elRow = p_myDataTable.getTrEl( elRow );

                if( elRow )
                {
                    var oRecord = p_myDataTable.getRecord(elRow);

                    switch( task.groupIndex )
                    {
                        case 0:
                            switch( task.index )
                            {
                                case 0:
                                    window.location.assign( "{/literal}{genUrl controller="customer" action="edit"}{literal}/id/"  + oRecord.getData( 'id' ) );
                                    break;

                                case 1:
                                    if( confirm("Are you sure you want to delete " + oRecord.getData( 'shortname' ) + "?" ) )
                                        window.location.assign( "{/literal}{genUrl controller="customer" action="delete"}{literal}/id/"  + oRecord.getData( 'id' ) );
                                    break;
                            }
                            break;
                        case 1:
                            switch( task.index )
                            {
                                case 0:
                                    window.location.assign( "{/literal}{genUrl controller="customer" action="send-welcome-email"}{literal}/id/"  + oRecord.getData( 'id' ) );
                                    break;
                            }
                            break;
                    }
                }
            }
        };

        var myContextMenu = new YAHOO.widget.ContextMenu( "mycontextmenu",
                {trigger:myDataTable.getTbodyEl()}
        );

        myContextMenu.addItem("Edit", 0);
        myContextMenu.addItem("Delete", 0);
        myContextMenu.addItem("Send welcome email...", 1);

        myContextMenu.render("dataTable");
        myContextMenu.clickEvent.subscribe( onContextMenuClick, myDataTable );

        return {
            oDS: myDataSource,
            oDT: myDataTable
        };
    });

})();
</script>
{/literal}



{include file="footer.tpl"}



