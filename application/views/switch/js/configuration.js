

var oDataTable;

$(document).ready(function() {

	oDataTable = $( '#frontend-list-table' ).dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "iDisplayLength": 50,
        "sPaginationType": "bootstrap",
        "aoColumns": [
            null,
            {if not isset( $switchid )}null,{/if}
            null,
            { "sSortDataType": "dom-text", "sType": "numeric" },
            {if not isset( $vlanid )}null,{/if}
            { "sSortDataType": "dom-text", "sType": "html" },
            null,
            null,
            null,
            null           
        ]
    });
    $( '#frontend-list-table' ).show();

});




