YAHOO.namespace( 'IXP' );

(function() {
    var Dom = YAHOO.util.Dom,
    Event = YAHOO.util.Event,
    queryString = '/output/json/',
    myDataSource = null,
    myDataTable = null;


    var getData = function( query ) {
        myDataSource.sendRequest( 
            'shortname/' + YAHOO.util.Dom.get( 'dt_input_sn' ).value
                + '/member/' + YAHOO.util.Dom.get( 'dt_input'    ).value
                + queryString,
            myDataTable.onDataReturnInitializeTable, myDataTable );
    };


    Event.onDOMReady(function() { 

        var oACDS = new YAHOO.util.FunctionDataSource( getData );
        oACDS.queryMatchContains = true;
        var oAutoComp = new YAHOO.widget.AutoComplete( "dt_input", "dataTable", oACDS);
        oAutoComp.minQueryLength = 0;

        var oACDSShortName = new YAHOO.util.FunctionDataSource( getData );
        oACDSShortName.queryMatchContains = true;
        var oAutoCompShortName = new YAHOO.widget.AutoComplete( "dt_input_sn", "dataTable", oACDSShortName );
        oAutoCompShortName.minQueryLength = 0;

        var makeEditButton = function( elCell, oRecord, oColumn, sData ) { 
            elCell.innerHTML = "<a href='virtual-interface/edit/id/" + sData + "'>edit</a>"; 
        }; 

        var makeDeleteButton = function( elCell, oRecord, oColumn, sData ) { 
            elCell.innerHTML = "<a href='virtual-interface/delete/id/" + sData + "' onclick='return confirm (\"Are you sure you want to delete this virtual interface and associated physical and vlan interfaces?\");' >delete</a>"; 
        }; 

        var makeMemberLink = function( elCell, oRecord, oColumn, sData ) { 
            elCell.innerHTML = "<span id=\"viewPanel-customer-" + oRecord.getData('memberid') + '-' + oColumn.getId() + '-' + oRecord.getId() + "\" class=\"blueLink\">"
                + sData + "</span>";

            YAHOO.util.Event.addListener( 
                'viewPanel-customer-' + oRecord.getData('memberid') + '-' + oColumn.getId() + '-' + oRecord.getId(), 
                'click', 
                YAHOO.IXP.showViewPanel, { 
                        controller: 'customer', 
                        id: oRecord.getData('memberid')
                    } 
                );
        };
 
        var makeLocationLink = function( elCell, oRecord, oColumn, sData ) { 
            elCell.innerHTML = "<span id=\"viewPanel-location-" + oRecord.getData('locationid') + '-' + oColumn.getId() + '-' + oRecord.getId() + "\" class=\"blueLink\">"
                + sData + "</span>";

            YAHOO.util.Event.addListener( 
                'viewPanel-location-' + oRecord.getData('locationid') + '-' + oColumn.getId() + '-' + oRecord.getId(), 
                'click', 
                YAHOO.IXP.showViewPanel, { 
                        controller: 'location', 
                        id: oRecord.getData('locationid')
                    } 
                );
        };
 
        var makeSwitchLink = function( elCell, oRecord, oColumn, sData ) { 
            elCell.innerHTML = "<span id=\"viewPanel-switchtable-" + oRecord.getData('switchid') + '-' + oColumn.getId() + '-' + oRecord.getId() + "\" class=\"blueLink\">"
                + sData + "</span>";

            YAHOO.util.Event.addListener( 
                'viewPanel-switchtable-' + oRecord.getData('switchid') + '-' + oColumn.getId() + '-' + oRecord.getId(), 
                'click', 
                YAHOO.IXP.showViewPanel, { 
                        controller: 'switch', 
                        id: oRecord.getData('switchid')
                    } 
                );
        };
 
        var myColumnDefs = [
            { key:"member",
                label:"Member",
                sortable:true,
                formatter:makeMemberLink
            },
            { key:"shortname",
                label:"Short Name",
                sortable:true,
                formatter:makeMemberLink
            },
            { key:"location",
                label:"Location", 
                sortable:true,
                formatter:makeLocationLink
            },
            { key:"switch",
                label:"Switch",
                sortable:true,
                formatter:makeSwitchLink
            },
            { key:"port",
                label:"Port",
                sortable:false
            },
            { key:"speed",
                label:"Speed",
                sortable:true
            },
            { key:"id",
                label:"",
                formatter: makeEditButton,
                sortable:false
            },
            { key:"id",
                label:"",
                formatter: makeDeleteButton,
                sortable:false
            }
        ];

        myDataSource = new YAHOO.util.DataSource( "virtual-interface/get-data/" );
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
                { key:"memberid",
                    parser:"number"
                },
                { key:"shortname",
                    parser:"string"
                },
                { key:"location",
                    parser:"string"
                },
                { key:"locationid",
                    parser:"number"
                },
                {key:"switch"},
                { key:"switchid",
                    parser:"number"
                },
                {key:"port"},
                {key:"speed"},
                {key:"id"},
                {key:"id"}
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


        return {
            oDS: myDataSource,
            oDT: myDataTable
        };
    });

})();
