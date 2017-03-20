

$(document).ready(function() {

	$( 'a[id|="object-delete"]' ).on( 'click', function( event ){

		event.preventDefault();
		url = $(this).attr( "data-url" );

	    bootbox.dialog( "Are you sure you want to delete this object?", [{
	    	"label": "Cancel",
	    	"class": "btn-primary"
	    },
	    {
	    	"label": "Delete",
	    	"class": "btn-danger",
	    	"callback": function() { document.location.href = url; }
	    }]);

    });

    $( '#tooltip-lag-framing' ).tooltip({
    	html:	true,
    	title:	"Indicates if operators / provisioning systems should enable LAG framing such as LACP.<br><br>" +
				"Mandatory where there is more than one phsyical interface.<br><br>" +
				"Otherwise optional where a member requests a single member LAG for ease of upgrades."
	});

    $( '#tooltip-trunk' ).tooltip({
        title:	"Indicates if operators / provisioning systems should configure this port with 802.1q framing / tagged packets."
    });
});




