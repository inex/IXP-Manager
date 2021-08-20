<script>

    let dd_pdb = $( '#pdb_facility_id' );
    let errorOption = `<option value="0">AJAX / API Error</option>\n`;

    $(document).ready(function() {
        dd_pdb.select2({
            placeholder: 'Please wait, loading...',
            allowClear: true
        });

        $.ajax( "<?= url('peering-db/fac') ?>" )
            .done( function( data ) {
                let selectedpdb, selectNow;
                let options = `<option value=''>Choose the matching PeeringDB Facility...</option>\n`;

                <?php if( $t->data[ 'params'][ 'object' ] && $t->data[ 'params'][ 'object' ]->pdb_facility_id ): ?>
                    selectedpdb = <?= $t->data[ 'params'][ 'object' ]->pdb_facility_id ?>;
                <?php elseif( Request::old('pdb_facility_id' ) ): ?>
                    selectedpdb = <?= Request::old('pdb_facility_id' ) ?>;
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
            })
            .fail( function() {
                dd_pdb.prop( 'disabled', true );
                <?php if( !$t->data[ 'params' ][ 'isAdd' ] && $t->data[ 'params'][ 'object']->pdb_facility_id ): ?>
                    errorOption = `<option value=''>Choose the matching PeeringDB facility...</option>\n`;
                    errorOption += `<option selected="selected" value="<?= $t->data[ 'params'][ 'object']->pdb_facility_id ?>">PeeringDB ID: <?= $t->data[ 'params'][ 'object']->pdb_facility_id ?></option>` ;
                    dd_pdb.prop( 'disabled', false );
                <?php endif; ?>
                dd_pdb.html( errorOption );

                $( '#form' ).prepend( `<div class="alert alert-danger" role="alert"> We could not load the list of facilities from PeeringDB.
                                        This is usually a transient network / service issue and should work again at a later stage.
                                        Please try again later and set the PeeringDB facility ID. </div>` );
            })
            .always( function() {
                dd_pdb.select2({ allowClear: true, placeholder: 'Choose the matching PeeringDB facility...' });
                dd_pdb.trigger( "changed.select2" );
                $( '#btn-submit' ).prop('disabled', false);
            });

        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" )
    });
</script>