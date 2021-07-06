<script>
    const dd_source   = $( '#source' );
    const btn_submit  = $( '#submit' );

    let tables    = <?= json_encode( $t->content->symbols->{'routing table'} ) ?>.sort();
    let protocols = <?= json_encode( $t->content->symbols->protocol ) ?>.sort();
    let source    = 'table';

    btn_submit.on( 'click', function( e ) {
        e.preventDefault();
        let net     = $( "#net" ).val().trim();
        let masklen = 32;
        if( net === "" ) {
            return;
        }
        btn_submit.prop('disabled', true);

        if( net.indexOf('/') !== -1 ) {
            masklen = net.substring( net.indexOf('/') + 1);
            net     = net.substring( 0, net.indexOf('/') );
        } else if( net.indexOf(':') !== -1 ) {
            masklen = 128;
        }

        $.get('<?= url('lg/' . $t->lg->router()->handle  . '/route') ?>/' + encodeURIComponent( net ) + '/' +
            encodeURIComponent( masklen ) + '/' +
            source + '/' + encodeURIComponent( dd_source.val() ), function( html ) {
                $( '#route-modal .modal-content' ).html( html );
                $( '#route-modal' ).modal( 'show', { backdrop: 'static' } );
            });

            btn_submit.prop('disabled', false);
        });

    $( 'input:radio[name="source_selector"]' ).change( function(){
        if( $( this ).is( ':checked' ) ) {
            dd_source.html( '' );
            if( $(this).val() === "table" ) {
                source = 'table'
                datas = tables;
            } else {
                source = 'protocol'
                datas = protocols;
            }

            datas.forEach( function( e ){
                $( "#source" ).append( `<option value="${e}">${e}</option>` );
            });

            if( $(this).val() === "table" ) {
                $( "#source" ).val('master<?= $t->lg->router()->protocol() ?>');
            }
        }
    });

    $(document).ready(function() {
        tables.forEach( function(e){
            dd_source.append( `<option value="${e}">${e}</option>` );
        });
        dd_source.val( 'master<?= $t->lg->router()->protocol() ?>' );
    });
</script>