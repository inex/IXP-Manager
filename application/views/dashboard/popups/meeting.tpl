
{literal}

<script type="text/javascript">

YAHOO.util.Event.onDOMReady( function() {


	// Define various event handlers for Dialog
	var handleAttending = function() {

        this.hide();

	    $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/attend"{literal},
	            function( data ) {
	                switch( data['response'] )
	                {
	                    case 1:
		                    alert( "We have recorded your intention to attend this meeting. Thanks!" );
	                        break;
	                    case 0:
                            alert( {/literal}"ERROR: We could no record your intention to attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}."{literal} );
	                        break;
	                }
	            }
	    );

	};

    var handleNotAttending = function() {
        this.hide();

        $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/noattend"{literal},
                function( data ) {
                    switch( data['response'] )
                    {
                        case 1:
                            alert( "We have recorded your intention to not attend this meeting. We hope you can make the next one." );
                            break;
                        case 0:
                            alert( {/literal}"ERROR: We could no record your intention to not attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}."{literal} );
                            break;
                    }
                }
        );

    };


    var handleSkip = function() {
        this.hide();

        $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/skip"{literal},
                    function( data ) {}
        );
    };

    var handleDontAsk = function() {
        this.hide();

        $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/dontask"{literal},
                    function( data ) {}
        );

        alert( "We will not ask you to RSVP for this meeting again. However, you can always do this later via the menu option Member Information -> Meetings" );
    };


	// Instantiate the Dialog
	IXP_Meeting_Dialog =
	    new YAHOO.widget.SimpleDialog( "meetingDialog",
	             {
	               width: "500px",
	               fixedcenter: true,
	               visible: false,
	               draggable: false,
	               modal: true,
	               close: false, {/literal}
	               text: "The next INEX members' meeting is scheduled for {$meeting.date|date_format}. "
		               + "Please <a href=\"{genUrl controller='meeting' action='read'}\">click here</a> "
		               + "for details or let us know if you can make it by choosing an option below:", {literal}
	               constraintoviewport: true,
	               buttons: [ { text:"Don't Ask Again", handler:handleDontAsk },
		      	              { text:"Attending", handler:handleAttending },
	                          { text:"Not Attending",  handler:handleNotAttending },
	                          { text:"Skip",  handler:handleSkip, isDefault:true } ]
	             } );



	IXP_Meeting_Dialog.setHeader("INEX Members' Meeting");

	IXP_Meeting_Dialog.render( document.body );

	IXP_Meeting_Dialog.show();

});


</script>

{/literal}
