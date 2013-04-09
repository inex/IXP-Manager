$(document).ready(function() {

    $( 'a[id|="cont-list-delete"]' ).off('click').on( 'click', function( event ){

	event.preventDefault();
	url = $(this).attr("href");

	bootbox.dialog( "Are you sure you want to delete this contact? Related user also will be deleted.", [{
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