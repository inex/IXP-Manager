
<script>
    let dd_pdb = $( '#pdb_facility_id' );

    $(document).ready(function() {

        $.ajax( "<?= url('api/v4/peering-db/fac') ?>" )
            .done( function( data ) {
                console.log( 'dd');
                let selectedpdb, selectNow;
                let options = `<option value=''>Choose the matching PeeringDB Facility...</option>\n`;

                <?php if( $t->data[ 'params'][ 'object' ] && $t->data[ 'params'][ 'object' ]->getPdbFacilityId() ): ?>
                    selectedpdb = <?= $t->data[ 'params'][ 'object' ]->getPdbFacilityId() ?>;
                <?php else: ?>
                    selectedpdb = false;
                <?php endif; ?>

                $.each( data, function ( i, pdb ) {
                    selectNow = null;
                    if( selectedpdb === pdb.id ){
                        selectNow = 'selected="selected"';
                    }
                    options += `<option ${selectNow} value="${pdb.id}">${pdb.name}</option>\n`;
                });
                dd_pdb.html( options );
                dd_pdb.attr("placeholder", "Choose the matching PeeringDB Facility...");
            })
            .fail( function() {
                throw new Error("Error running ajax query for PeeringDB Facility");
            })
            .always( function() {
                dd_pdb.trigger( "changed.select2" );
                $( '#btn-submit' ).prop('disabled', false);
            });

    });

</script>
