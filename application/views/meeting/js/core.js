
$( '#rsvp_attending_{$e->getId()}' ).click( function() {

    $( '#rsvp_div_{$e->getId()}' ).hide( 400 );
	$( '#rsvp_div_{$e->getId()}' ).html('');

    $.getJSON( "{genUrl controller='meeting' action='rsvp'}/id/{$e->getId()}/answer/attend",
            function( data ) {
                switch( data['response'] )
                {
                    case 1:
                        alert( "We have recorded your intention to attend this meeting. Thanks!" );
                        break;
                    case 0:
                        alert( "ERROR: We could no record your intention to attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}." );
                        break;
                }
            }
    );
});

$( '#rsvp_not_attending_{$e->getId()}' ).click( function() {

    $( '#rsvp_div_{$e->getId()}' ).hide( 400 );
    $( '#rsvp_div_{$e->getId()}' ).html('');

    $.getJSON( "{genUrl controller='meeting' action='rsvp'}/id/{$e->getId()}/answer/noattend",
            function( data ) {
                switch( data['response'] )
                {
                    case 1:
                        alert( "We have recorded your intention to not attend this meeting. We hope you can make the next one." );
                        break;
                    case 0:
                        alert( "ERROR: We could no record your intention to not attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}." );
                        break;
                }
            }
    );
});

