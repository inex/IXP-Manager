
/**
 * Sorts a column containing IP addresses in typical dot notation. This can
 * be most useful when using DataTables for a networking application, and
 * reporting information containing IP address. Also has a matching type
 * detection plug-in for automatic type detection.
 *
 *  @name IP addresses
 *  @summary Sort IP addresses numerically
 *  @author Brad Wasson
 *
 *  @example
 *    $('#example').dataTable( {
 *       columnDefs: [
 *         { type: 'ip-address', targets: 0 }
 *       ]
 *    } );
 */

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "ip-address-pre": function ( a ) {
        var m = a.split("."), x = "";

        for(var i = 0; i < m.length; i++) {
            var item = m[i];
            if(item.length == 1) {
                x += "00" + item;
            } else if(item.length == 2) {
                x += "0" + item;
            } else {
                x += item;
            }
        }

        return x;
    },

    "ip-address-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "ip-address-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );



var oDataTable;

$(document).ready(function() {

    $( 'a[id|="list-delete"]' ).on( 'click', function( event ){

        event.preventDefault();
        url = $(this).attr("href");

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


    oDataTable = $( '#frontend-list-table' ).dataTable({
        'fnDrawCallback': function() {
                if( oss_prefs != undefined && 'iLength' in oss_prefs && oss_prefs['iLength'] != $( "select[name='frontend-list-table_length']" ).val() )
                {
                    oss_prefs['iLength'] = parseInt( $( "select[name='frontend-list-table_length']" ).val() );
                    $.jsonCookie( 'oss_prefs', oss_prefs, oss_cookie_options );
                }
            },
        'iDisplayLength': ( typeof oss_prefs != 'undefined' && 'iLength' in oss_prefs )
                ? oss_prefs['iLength']
                : {if isset( $options.defaults.table.entries )}{$options.defaults.table.entries}{else}10{/if},
        "aLengthMenu": [ [ 10, 25, 50, 100, 500, -1 ], [ 10, 25, 50, 100, 500, "All" ] ],
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "bAutoWidth": false,
        'aaSorting': [[ 0, 'asc' ]],
        'aoColumns': [
            { 'sType': 'ip-address' },
            null,
            null,
            { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
        ]
    });

    $( '#frontend-list-table' ).show();

});
