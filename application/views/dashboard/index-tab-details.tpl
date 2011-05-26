            <h2>NOC Details</h2>

            <script type="text/javascript">
                YAHOO.namespace( 'IXP' );

                YAHOO.IXP.MemberNocData = {ldelim}
                    details: [
                        {ldelim}attribute:"nocphone",    label:"<strong>Phone</strong>",         value:"{$customer.nocphone}"{rdelim},
                        {ldelim}attribute:"noc24hphone", label:"<strong>24 Hour Phone</strong>", value:"{$customer.noc24hphone}"{rdelim},
                        {ldelim}attribute:"nocfax",      label:"<strong>Fax</strong>",           value:"{$customer.nocfax}"{rdelim},
                        {ldelim}attribute:"nocemail",    label:"<strong>Email</strong>",         value:"{$customer.nocemail}"{rdelim},
                        {ldelim}attribute:"nochours",    label:"<strong>Hours</strong>",         value:"{$customer.nochours}"{rdelim},
                        {ldelim}attribute:"nocwww",      label:"<strong>WWW</strong>",           value:"{$customer.nocwww}"{rdelim}
                    ]
                {rdelim}

                YAHOO.util.Event.addListener( window, "load", function() {ldelim}
                    YAHOO.IXP.NocDetailsEditableTable = function() {ldelim}

                        var dataSaved = function( fnCallback, oNewValue ) {ldelim}

                        var record = this.getRecord(),
                            column = this.getColumn(),
                            oldValue = this.value,
                            datatable = this.getDataTable();

                            if( oldValue == oNewValue )
                            {ldelim}
                                fnCallback( false );
                            {rdelim}

                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">'
                                + '<img src="images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
                                + '&nbsp;Processing....</div>';

                            var aniObj = new YAHOO.util.Anim(
                                document.getElementById( "ajaxMessage" ),
                                {ldelim} opacity: {ldelim}from: 1, to: 0 {rdelim} {rdelim},
                                '10',
                                YAHOO.util.Easing.easeOut
                            );

                            YAHOO.util.Connect.asyncRequest(
                                'POST',
                                '{genUrl controller="customer" action="update-attribute" }',
                                {ldelim}
                                    success: function(o) {ldelim}

                                        var dialogHandleSuccessClearDiv = function() {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '';
                                            YAHOO.util.Dom.setStyle( document.getElementById( "ajaxMessage" ), 'opacity', '1' );
                                        {rdelim}

                                        if( o.responseText.substr( 0, 2 ) == '1:' )
                                        {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">' + o.responseText.substr( 2 ) + '</div>';
                                            fnCallback( true, oNewValue );
                                        {rdelim}
                                        else
                                        {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">' + o.responseText.substr( 2 ) + '</div>';
                                            fnCallback( false, oNewValue );
                                        {rdelim}

                                        aniObj.onComplete.subscribe( dialogHandleSuccessClearDiv );
                                        aniObj.animate();

                                    {rdelim},

                                    failure:function(o) {ldelim}
                                        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">Error with AJAX communication.</div>';
                                        fnCallback( false );
                                    {rdelim},
                                    scope:this
                                {rdelim},
                                'attribute=' + record.getData( 'attribute' ) + '&id=' + {$customer.id} + '&newValue=' + oNewValue
                            );

                            fnCallback( false, oNewValue );
                        {rdelim}

                        var nocColumnDefs = [
                            {ldelim}key:"label", label:"Attribute"{rdelim},
                            {ldelim}key:"value", label:"Value", editor: new YAHOO.widget.TextareaCellEditor( {ldelim}asyncSubmitter: dataSaved{rdelim} ){rdelim}
                        ];

                        var nocDataSource = new YAHOO.util.DataSource( YAHOO.IXP.MemberNocData.details );
                        nocDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
                        nocDataSource.responseSchema = {ldelim}
                            fields: [ "label","value","attribute"]
                        {rdelim};

                        var nocDataTable = new YAHOO.widget.DataTable( "nocDetailsTable", nocColumnDefs, nocDataSource, {ldelim}{rdelim});

                        // Set up editing flow
                        var highlightEditableCell = function(oArgs) {ldelim}
                            var elCell = oArgs.target;
                            if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {ldelim}
                                this.highlightCell(elCell);
                            {rdelim}
                        {rdelim};

                        nocDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
                        nocDataTable.subscribe("cellMouseoutEvent", nocDataTable.onEventUnhighlightCell);
                        nocDataTable.subscribe("cellClickEvent", nocDataTable.onEventShowCellEditor);

                        return {ldelim}
                            oDS: nocDataSource,
                            oDT: nocDataTable
                        {rdelim};
                    {rdelim}();
                {rdelim});


            </script>

            <blockquote>
                <div id="nocDetailsTable"></div>
            </blockquote>







            <h2>Billing Details</h2>

            <script type="text/javascript">
                YAHOO.namespace( 'IXP' );

                YAHOO.IXP.MemberBillingData = {ldelim}
                    details: [
                        {ldelim}attribute:"billingContact",  label:"<strong>Contact Person</strong>",   value:"{$customer.billingContact}"{rdelim},
                        {ldelim}attribute:"billingAddress1", label:"<strong>Address Line #1</strong>",  value:"{$customer.billingAddress1}"{rdelim},
                        {ldelim}attribute:"billingAddress1", label:"<strong>Address Line #2</strong>",  value:"{$customer.billingAddress2}"{rdelim},
                        {ldelim}attribute:"billingCity",     label:"<strong>City</strong>",             value:"{$customer.billingCity}"{rdelim},
                        {ldelim}attribute:"billingCountry",  label:"<strong>Country</strong>",          value:"{$customer.billingCountry}"{rdelim}
                    ]
                {rdelim}

                YAHOO.util.Event.addListener( window, "load", function() {ldelim}
                    YAHOO.IXP.BillingDetailsEditableTable = function() {ldelim}

                        var dataSaved = function( fnCallback, oNewValue ) {ldelim}

                        var record = this.getRecord(),
                            column = this.getColumn(),
                            oldValue = this.value,
                            datatable = this.getDataTable();

                            if( oldValue == oNewValue )
                            {ldelim}
                                fnCallback( false );
                            {rdelim}

                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">'
                                + '<img src="images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
                                + '&nbsp;Processing....</div>';

                            var aniObj = new YAHOO.util.Anim(
                                document.getElementById( "ajaxMessage" ),
                                {ldelim} opacity: {ldelim}from: 1, to: 0 {rdelim} {rdelim},
                                '10',
                                YAHOO.util.Easing.easeOut
                            );

                            YAHOO.util.Connect.asyncRequest(
                                'POST',
                                '{genUrl controller="customer" action="update-attribute" }',
                                {ldelim}
                                    success: function(o) {ldelim}

                                        var dialogHandleSuccessClearDiv = function() {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '';
                                            YAHOO.util.Dom.setStyle( document.getElementById( "ajaxMessage" ), 'opacity', '1' );
                                        {rdelim}

                                        if( o.responseText.substr( 0, 2 ) == '1:' )
                                        {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">' + o.responseText.substr( 2 ) + '</div>';
                                            fnCallback( true, oNewValue );
                                        {rdelim}
                                        else
                                        {ldelim}
                                            document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">' + o.responseText.substr( 2 ) + '</div>';
                                            fnCallback( false, oNewValue );
                                        {rdelim}

                                        aniObj.onComplete.subscribe( dialogHandleSuccessClearDiv );
                                        aniObj.animate();

                                    {rdelim},

                                    failure:function(o) {ldelim}
                                        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">Error with AJAX communication.</div>';
                                        fnCallback( false );
                                    {rdelim},
                                    scope:this
                                {rdelim},
                                'attribute=' + record.getData( 'attribute' ) + '&id=' + {$customer.id} + '&newValue=' + oNewValue
                            );

                            fnCallback( false, oNewValue );
                        {rdelim}

                        var billingColumnDefs = [
                            {ldelim}key:"label", label:"Attribute"{rdelim},
                            {ldelim}key:"value", label:"Value", editor: new YAHOO.widget.TextareaCellEditor( {ldelim}asyncSubmitter: dataSaved{rdelim} ){rdelim}
                        ];

                        var billingDataSource = new YAHOO.util.DataSource( YAHOO.IXP.MemberBillingData.details );
                        billingDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
                        billingDataSource.responseSchema = {ldelim}
                            fields: [ "label","value","attribute"]
                        {rdelim};

                        var billingDataTable = new YAHOO.widget.DataTable( "billingDetailsTable", billingColumnDefs, billingDataSource, {ldelim}{rdelim});

                        // Set up editing flow
                        var highlightEditableCell = function(oArgs) {ldelim}
                            var elCell = oArgs.target;
                            if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {ldelim}
                                this.highlightCell(elCell);
                            {rdelim}
                        {rdelim};

                        billingDataTable.subscribe("cellMouseoverEvent", highlightEditableCell);
                        billingDataTable.subscribe("cellMouseoutEvent", billingDataTable.onEventUnhighlightCell);
                        billingDataTable.subscribe("cellClickEvent", billingDataTable.onEventShowCellEditor);

                        return {ldelim}
                            oDS: billingDataSource,
                            oDT: billingDataTable
                        {rdelim};
                    {rdelim}();
                {rdelim});


            </script>

            <blockquote>
                <div id="billingDetailsTable"></div>
            </blockquote>
