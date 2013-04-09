$(document).ready(function() {

	$( 'a[id|="list-delete"]' ).off('click').on( 'click', function( event ){

		event.preventDefault();
		url = $(this).attr("href");

	    bootbox.dialog( "Are you sure you want to delete this contact group? All relations with contacts in this group will be lost.", [{
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




