
<script>

    let dd_pdb = $( '#pdb_facility_id' );
    let errorOption = `<option value="0">Error</option>\n`;

    $(document).ready(function() {

        dd_pdb.select2({
            placeholder: 'Please wait, loading...',
            allowClear: true
        });

        $.ajax( "<?= url('api/v4/peering-db/fac') ?>" )
            .done( function( data ) {
                let selectedpdb, selectNow;
                dd_pdb.select2({allowClear: true,placeholder: 'Choose the matching PeeringDB Facility...'});
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
                dd_pdb.prop( 'disabled', true );
                <?php if( !$t->data[ 'params' ][ 'isAdd' ] ): ?>
                    <?php if( $t->data[ 'params'][ 'object']->getPdbFacilityId() ): ?>
                        errorOption = `<option value="<?= $t->data[ 'params'][ 'object']->getPdbFacilityId() ?>"> PeeringDB ID: <?= $t->data[ 'params'][ 'object']->getPdbFacilityId() ?></option>` ;
                        dd_pdb.prop( 'disabled', false );
                    <?php endif; ?>
                <?php endif; ?>
                dd_pdb.html( errorOption );

                $( '#form' ).prepend( `<div class="alert alert-danger" role="alert"> We could not load the list of facilities from PeeringDB.
                                        This is usually a transient network / service issue and should work again at a later stage.
                                        Please try again later and set the PeeringDB facility ID. </div>` );
            })
            .always( function() {
                dd_pdb.trigger( "changed.select2" );
                $( '#btn-submit' ).prop('disabled', false);
            });

    });

</script>
