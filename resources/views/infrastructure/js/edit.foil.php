
<script>
    let dd_ixp = $( '#ixf_ix_id' );
    let dd_pdb = $( '#pdb_ixp' );

    let ixp_req_finish = false;
    let pdb_req_finish = false;

    $(document).ready(function() {

        $.ajax( "<?= url('api/v4/ix-f/ixp') ?>" )
            .done( function( data ) {
                let selectedixp, selectNow;
                let options = `<option value=''>Choose the matching IX-F IXP...</option>\n`;

                <?php if( $t->params[ 'inf' ] && $t->params[ 'inf' ]->getIxfIxId() ): ?>
                    selectedixp = <?= $t->params[ 'inf' ]->getIxfIxId() ?>;
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
                throw new Error("Error running ajax query for IX-F IXPs");
            })
            .always( function() {
                dd_ixp.trigger( "changed.select2" );
                ixp_req_finish = true;

                if( pdb_req_finish ){
                    $( '#btn-submit' ).prop('disabled', false);
                }
            });


        $.ajax( "<?= url('api/v4/peeringdb/ix') ?>" )
            .done( function( data ) {
                let selectedpdb, selectNow;
                let options = `<option value=''>Choose the matching PeeringDB IXP...</option>\n`;

                <?php if( $t->params[ 'inf' ] && $t->params[ 'inf' ]->getPeeringdbIxId() ): ?>
                    selectedpdb = <?= $t->params[ 'inf' ]->getPeeringdbIxId() ?>;
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
                throw new Error("Error running ajax query for PeeringDB IXPs");
            })
            .always( function() {
                dd_pdb.trigger( "changed.select2" );
                pdb_req_finish = true;

                if( ixp_req_finish ){
                    $( '#btn-submit' ).prop('disabled', false);
                }
            });
    });

</script>
