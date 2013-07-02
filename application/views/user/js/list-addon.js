$(document).ready(function() {

    $( 'a[id|="list-delete"]' ).off('click').on( 'click', function( event ){

        event.preventDefault();
        url = $(this).attr("href");

        bootbox.dialog( "Are you sure you want to delete this user?", [{
            "label": "Cancel",
            "class": "btn-primary"
        },
        {
            "label": "Remove User Access",
            "class": "btn-danger",
            "callback": function() { document.location.href = url + "/useronly/1"; }
        },
        {
            "label": "Delete",
            "class": "btn-danger",
            "callback": function() { document.location.href = url }
        }
        ]);

    });
});




