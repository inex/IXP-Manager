
{literal}

// Define various event handlers for Dialog
var dialogHandleSubmit = function() {
    document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">'
        + '<img src="images/throbber-small.gif" width="16" height="16" border="0" alt="[...]" />'
        + '&nbsp;Processing....</div>';
    this.submit();
};
var dialogHandleCancel = function() {
    this.cancel();
};

var aniObj = new YAHOO.util.Anim(
    document.getElementById( "ajaxMessage" ),
    { opacity: {from: 1, to: 0 } },
    '10',
    YAHOO.util.Easing.easeOut
);

var dialogHandleSuccess = function(o) {

    var dialogHandleSuccessClearDiv = function() {
        document.getElementById( "ajaxMessage" ).innerHTML = '';
        YAHOO.util.Dom.setStyle( document.getElementById( "ajaxMessage" ), 'opacity', '1' );
    }

    if( o.responseText.substr( 0, 2 ) == '1:' )
    {
        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-success">' + o.responseText.substr( 2 ) + '</div>';
    }
    else
    {
        document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">' + o.responseText.substr( 2 ) + '</div>';
    }

    aniObj.onComplete.subscribe( dialogHandleSuccessClearDiv );
    aniObj.animate();

};

var dialogHandleFailure = function(o) {
    document.getElementById( "ajaxMessage" ).innerHTML = '<div class="message message-error">Error with AJAX communication.</div>';
};

var sendSMSDialog = new YAHOO.widget.Dialog( "sendSMSDialog",
                    {
                        width : "350px",
                        fixedcenter : true,
                        visible : false,
                        constraintoviewport : true,
                        buttons : [
                            { text:"Submit", handler:dialogHandleSubmit, isDefault:true },
                            { text:"Cancel", handler:dialogHandleCancel }
                        ]
                    }
);


// Wire up the success and failure handlers
sendSMSDialog.callback = {
        success: dialogHandleSuccess,
        failure: dialogHandleFailure
};

// Render the Dialog
sendSMSDialog.render();









var sendEmailDialog = new YAHOO.widget.Dialog( "sendEmailDialog",
                    {
                        width : "550px",
                        fixedcenter : true,
                        visible : false,
                        constraintoviewport : true,
                        buttons : [
                            { text:"Submit", handler:dialogHandleSubmit, isDefault:true },
                            { text:"Cancel", handler:dialogHandleCancel }
                        ]
                    }
);


// Wire up the success and failure handlers
sendEmailDialog.callback = {
        success: dialogHandleSuccess,
        failure: dialogHandleFailure
};

// Render the Dialog
sendEmailDialog.render();




var onContextMenuClick = function( p_sType, p_aArgs, p_myDataTable )
{
    // complete the animation of any previously selected options
    aniObj.stop();
    document.getElementById( "ajaxMessage" ).innerHTML = '';
    YAHOO.util.Dom.setStyle( document.getElementById( "ajaxMessage" ), 'opacity', '1' );

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
                            window.location.assign( "/ixp/{/literal}{$controller}{literal}/edit/id/"  + oRecord.getData( 'id' ) );
                            break;

                        case 1:
                            if( confirm("Are you sure you want to delete this record?" ) )
                                window.location.assign( "/ixp/{/literal}{$controller}{literal}/delete/id/"  + oRecord.getData( 'id' ) );
                            break;
                    }
                    break;

                case 1:
                    switch( task.index )
                    {
                        case 0: /* Send Password via SMS */
                            document.getElementById( "sendSMSDialog-id" ).value = oRecord.getData( 'id' );
                            document.getElementById( "sendSMSDialog-to" ).value = oRecord.getData( 'authorisedMobile' );
                            document.getElementById( "sendSMSDialog-message" ).value =
                                "Your INEX Members' Area password is:\n\n" + oRecord.getData( 'password' ) + "\n\nSincerely,\nINEX Operations\noperations@inex.ie";
                            document.getElementById( "sendSMSDialog-count" ).innerHTML =
                                document.getElementById( "sendSMSDialog-message" ).value.length;

                            sendSMSDialog.show();
                            break;

                        case 1: /* Send Login Details via Email */
                            document.getElementById( "sendEmailDialog-id" ).value = oRecord.getData( 'id' );
                            document.getElementById( "sendEmailDialog-to" ).value = oRecord.getData( 'email' );

                            var loginDetailsString =
                                "<p>Dear INEX Member,</p><p>Your INEX Members' Area username is:</p><p><code>" + oRecord.getData( 'username' )
                                + "</code></p><p>We will send your password to the authorised mobile phone (" + oRecord.getData( 'authorisedMobile' ) + ") by SMS."
                                + "</p><p>Sincerely,<br />INEX Operations<br /><a href=\"mailto:operations@inex.ie\">operations@inex.ie</a></p>";

                            document.getElementById( "sendEmailDialog-message" ).value = loginDetailsString;
                            emailEditor.setEditorHTML( loginDetailsString );

                            sendEmailDialog.show();
                            break;
                    }
                    break

                case 2:
                    switch( task.index )
                    {
                        case 0: /* Log in as... SMS */
                            window.location.assign( "{/literal}{genUrl controller='auth' action='switch'}{literal}/id/"  + oRecord.getData( 'id' ) );
                            break;
                    }

            }
        }
    }
};

var myContextMenu = new YAHOO.widget.ContextMenu( "mycontextmenu",
        {trigger:this.myDataTable.getTbodyEl()}
);

myContextMenu.addItem("Edit", 0   );
myContextMenu.addItem("Delete", 0 );

myContextMenu.addItem("Send Password via SMS", 1 );
myContextMenu.addItem("Send Login Details via Email", 1 );

myContextMenu.addItem("Log in as...", 2 );

myContextMenu.render("myDatatable");
myContextMenu.clickEvent.subscribe( onContextMenuClick, this.myDataTable );



{/literal}
