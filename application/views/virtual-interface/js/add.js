

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
});




