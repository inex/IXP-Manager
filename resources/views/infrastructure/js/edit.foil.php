<script>
    let dd_ixp = $( '#ixf_ix_id' );
    let dd_pdb = $( '#peeringdb_ix_id' );

    let ixp_req_finish = false;
    let pdb_req_finish = false;
    let errorOption = `<option value="0">AJAX / API Error</option>\n`;

    dd_ixp.select2({
        placeholder: 'Please wait, loading...',
        allowClear: true,
        width: '100%',
    });

    dd_pdb.select2({
        placeholder: 'Please wait, loading...',
        allowClear: true,
        width: '100%',
    });

    $(document).ready(function() {
        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" )

        $.ajax( "<?= url('api/v4/ix-f/ixp') ?>" )
            .done( function( data ) {
                let selectedixp, selectNow;
                let options = `<option value=''>Choose the matching IX-F IXP...</option>\n`;

                <?php if( $t->data[ 'params'][ 'object' ] && $t->data[ 'params'][ 'object' ]->ixf_ix_id ): ?>
                    selectedixp = <?= $t->data[ 'params'][ 'object' ]->ixf_ix_id ?>;
                <?php elseif( Request::old('ixf_ix_id' ) ): ?>
                    selectedixp = <?= Request::old('ixf_ix_id' ) ?>;
                <?php else: ?>
                    selectedixp = false;
                <?php endif; ?>

                $.each( data, function ( i, ixp ) {
                    selectNow = null;
                    if( selectedixp === ixp.ixf_id ){
                        selectNow = 'selected="selected"';
                    }
                    options += `<option ${selectNow} value="${ixp.ixf_id}">${ixp.name}</option>\n`;
                });
                dd_ixp.html( options );
                dd_ixp.attr("placeholder", "Choose the matching IX-F IXP...");
            })
            .fail( function() {
                dd_ixp.prop( 'disabled', true );
                <?php if( !$t->data[ 'params' ][ 'isAdd' ] && $t->data[ 'params'][ 'object']->ixf_ix_id ): ?>
                    errorOption = `<option value="<?= $t->data[ 'params'][ 'object']->ixf_ix_id ?>"> IX-F IXP ID: <?= $t->data[ 'params'][ 'object']->ixf_ix_id ?></option>` ;
                    dd_ixp.prop( 'disabled', false );
                <?php endif; ?>
                dd_ixp.html( errorOption );

                $( '#form' ).prepend( `<div class="alert alert-danger" role="alert"> We could not load the list of IXPs from IX-F.
                                        This is usually a transient network / service issue and should work again at a later stage.
                                        Please try again later and set the IX-F IXP. </div>` );
            })
            .always( function() {
                dd_ixp.select2({ allowClear: true, placeholder: 'Choose the matching IX-F IXP...' });
                dd_ixp.trigger( "changed.select2" );
                ixp_req_finish = true;

                if( pdb_req_finish ){
                    $( '#btn-submit' ).prop('disabled', false);
                }
            });


        $.ajax( "<?= url('peeringdb/ix') ?>" )
            .done( function( data ) {
                let selectedpdb, selectNow;
                let options = `<option value=''>Choose the matching PeeringDB IXP...</option>\n`;

                <?php if( $t->data[ 'params'][ 'object' ] && $t->data[ 'params'][ 'object' ]->peeringdb_ix_id ): ?>
                    selectedpdb = <?= $t->data[ 'params'][ 'object' ]->peeringdb_ix_id ?>;
                <?php elseif( Request::old('peeringdb_ix_id' ) ): ?>
                    selectedpdb = <?= Request::old('peeringdb_ix_id' ) ?>;
                <?php else: ?>
                    selectedpdb = false;
                <?php endif; ?>

                $.each( data, function ( i, ixp ) {
                    selectNow = '';
                    if( selectedpdb === ixp.pdb_id ){
                        selectNow = 'selected="selected"';
                    }
                    options += `<option ${selectNow} value="${ixp.pdb_id}">${ixp.name}</option>\n`;
                });
                dd_pdb.html( options );
            })
            .fail( function() {
                dd_pdb.prop( 'disabled', true );
                <?php if( !$t->data[ 'params' ][ 'isAdd' ] && $t->data[ 'params'][ 'object']->peeringdb_ix_id ): ?>
                    errorOption = `<option value="<?= $t->data[ 'params'][ 'object']->peeringdb_ix_id ?>"> PeeringDB IXP ID: <?= $t->data[ 'params'][ 'object']->peeringdb_ix_id ?></option>` ;
                    dd_pdb.prop( 'disabled', false );
                <?php endif; ?>
                dd_pdb.html( errorOption );

                $( '#form' ).prepend( `<div class="alert alert-danger" role="alert"> We could not load the list of IXPs from PeeringDB.
                                        This is usually a transient network / service issue and should work again at a later stage.
                                        Please try again later and set the PeeringDB IXP. </div>` );
            })
            .always( function() {
                dd_pdb.select2({ allowClear: true, placeholder: 'Choose the matching PeeringDB IXP...' });
                dd_pdb.trigger( "changed.select2" );
                pdb_req_finish = true;

                if( ixp_req_finish ){
                    $( '#btn-submit' ).prop('disabled', false);
                }
            });
    });

</script>