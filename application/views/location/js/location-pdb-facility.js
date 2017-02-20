
// Try and load the facility list from PeeringDB
var jqxhr = $.getJSON( "{genUrl}/location/get-peering-db-facilities", function( json ) {

    var pdb_facility_id = $( '#pdb_facility_id').val();

    $('#select_pdb_facility_id').append( '<option></option>' );

    $.each( json.data, function ( i, fac ) {
        $('#select_pdb_facility_id').append($('<option>', {
            id       : 'pdb_facility_id_' + fac.id,
            value    : fac.id,
            text     : fac.name
        }));

        if( fac.id == pdb_facility_id )
            $( '#' + 'pdb_facility_id_' + fac.id ).attr( 'selected','selected' );

    });

    $( '#loading_pdb_facility_id' ).hide();
    $( '#dd_pdb_facility_id' ).show( function() {
        $( "#select_pdb_facility_id" ).chosen( { allow_single_deselect: true } ).change( function( evt, params ) {
            if( params != undefined ) {
                $('#pdb_facility_id').val(params.selected);
            } else {
                $('#pdb_facility_id').val("");
            }
        });
    });

});
