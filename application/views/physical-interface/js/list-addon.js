$(document).ready(function() {

      $( 'a[id|="list-delete"]' ).off('click').on( 'click', function( event ){

        event.preventDefault();
        var url = $(this).attr("href");
        var reltype = "normal";
        
        if( $( this ).attr( "data-type" ) == 1 )
            reltype = "fanout";
        if( $( this ).attr( "data-type" ) == 6 )
            reltype = "peering";
        
        var related = false;
        if( $( this ).attr( "data-related" ) )
            related = true;
        
        if( !related )
        {
            bootbox.dialog( "Are you sure you want to delete this object?", [{
                "label": "Cancel",
                "class": "btn-primary"
            },
            {
                "label": "Delete",
                "class": "btn-danger",
                "callback": function() { document.location.href = url; }
            }]);
        }
        else
        {
            bootbox.dialog( "Are you sure you want to delete this object? It has realted " + reltype  + " interface.", [{
                "label": "Cancel",
                "class": "btn-primary"
            },
            {
                "label": "Delete with related " + reltype + " inetrface",
                "class": "btn-danger",
                "callback": function() { document.location.href = url + "/related/1"; }
            },
            {
                "label": "Delete",
                "class": "btn-danger",
                "callback": function() { document.location.href = url; }
            }]);
        }

  });
});