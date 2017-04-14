$(document).ready(function() {

    // Try and load the facility list from PeeringDB
    $.getJSON( "{route('api-v4-ixf-ixs')}" )
        .done( function( ixs ) {

            var selectIxp = $( '#select-ixp');

            selectIxp.html( '<option></option>' );

            var curIxfId = $( '#ixf_ix_id' ).val();

            $.each( ixs, function ( i, ixp ) {
                selectIxp.append($('<option>', {
                    id       : 'ixf_opt_id_' + ixp.ixf_id,
                    value    : ixp.ixf_id,
                    text     : ixp.name
                }));

               if( ixp.ixf_id == curIxfId ) {
                   $('#' + 'ixf_opt_id_' + ixp.ixf_id).attr('selected', 'selected');
                   $('#ixf_id').html(ixp.ixf_id);
               }
            });

            selectIxp.chosen().change( function( evt, params ) {
                console.log(params);
                if( params != undefined ) {
                    $('#ixf_id').html(params.selected + " <b>(updated above!)</b>");
                    $( '#ixf_ix_id' ).val(params.selected);
                } else {
                    $('#ixf_id').html("" + " <b>(updated above!)</b>");
                    $( '#ixf_ix_id' ).val('');
                }
            });
        })
        .fail( function() {
            alert("Could not load IX's via IX-F DB :-(");
        });

    // Try and load the facility list from PeeringDB
    $.getJSON( "{route('api-v4-peeringdb-ixs')}" )
        .done( function( ixs ) {

            var selectIxp = $( '#select-pdb-ixp');

            selectIxp.html( '<option></option>' );

            var curPdbId = $( '#peeringdb_ix_id' ).val();

            $.each( ixs, function ( i, ixp ) {
                selectIxp.append($('<option>', {
                    id       : 'pdb_opt_id_' + ixp.pdb_id,
                    value    : ixp.pdb_id,
                    text     : ixp.name
                }));

                if( ixp.pdb_id == curPdbId ) {
                    $('#' + 'pdb_opt_id_' + ixp.pdb_id).attr('selected', 'selected');
                    $('#pdb_id').html(ixp.pdb_id);
                }
            });

            selectIxp.chosen().change( function( evt, params ) {
                console.log(params);
                if( params != undefined ) {
                    $('#pdb_id').html(params.selected + " <b>(updated above!)</b>");
                    $( '#peeringdb_ix_id' ).val(params.selected);
                } else {
                    $('#pdb_id').html("" + " <b>(updated above!)</b>");
                    $( '#peeringdb_ix_id' ).val('');
                }
            });
        })
        .fail( function() {
            alert("Could not load IX's via PeeringDB :-(");
        });

});
