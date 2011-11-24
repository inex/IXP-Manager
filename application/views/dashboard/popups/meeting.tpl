
<div id="dialog-meeting" title="INEX Members' Meeting" style="display: none;">
	<p>
        The next INEX members' meeting is scheduled for {$meeting.date|date_format}. 
        Please <a href="{genUrl controller='meeting' action='read'}">click here</a> 
        for details or let us know if you can make it by choosing an option below.
	</p>
</div>

<div id="dialog-meeting-skip" title="INEX Members' Meeting" style="display: none;">
	<p>
		We will not ask you to RSVP for this meeting again. However, you can always do 
		this later via the menu option <em>Member Information -> Meetings</em>.	
	</p>
</div>

{literal}

<script type="text/javascript">

$(document).ready( function() {


	// Define various event handlers for Dialog
	var handleAttending = function() {

    	$( "#dialog-meeting" ).dialog( 'close' );

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
    	$( "#dialog-meeting" ).dialog( 'close' );

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
    	$( "#dialog-meeting" ).dialog( 'close' );

        $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/skip"{literal},
                    function( data ) {}
        );
    };

    var handleDontAsk = function() {
    	$( "#dialog-meeting" ).dialog( 'close' );

        $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$meeting.id}/answer/dontask"{literal},
                    function( data ) {}
        );


		$( "#dialog-meeting-skip" ).dialog( 'open' );
    };


	$( "#dialog-meeting" ).dialog({
		resizable: false,
		modal: true,
		width: "500px",
		buttons: {
			"Don't Ask Again": handleDontAsk,
			"Attending": handleAttending,
			"Not Attending": handleNotAttending,
			"Skip": handleSkip
		}
	}).show();
	
	$( "#dialog-meeting-skip" ).dialog({
		autoOpen: false,
		modal: true,
		width: "400px",
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
});


</script>

{/literal}
