$(document).ready( function() {

    $( 'a[id|="usr-list-delete"]' ).off( 'click' ).on(  'click', function( event ) {

		event.preventDefault();
		url = $(this).attr( "href" );
	
		bootbox.dialog( "Are you sure you want to delete this contact?", 
			[
			 	{
				    "label": "Cancel",
				    "class": "btn-primary"
				},
				{
				    "label": "Remove User Access Only",
				    "class": "btn-danger",
				    "callback": function() { document.location.href = url + "/useronly/1"; }
				},
				{
				    "label": "Delete Contact",
				    "class": "btn-danger",
				    "callback": function() { document.location.href = url; }
				}
			]
		);
    });
});