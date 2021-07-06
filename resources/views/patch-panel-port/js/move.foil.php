<?php
    $hasSlave = $t->ppp->duplexSlavePorts()->exists();
?>
<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const dd_pp         = $( "#patch_panel_id" );
    const dd_master     = $( "#port_id" );
    const dd_slave      = $( "#slave_id" );
    const area_slave    = $( "#area_slave" );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:
    <?php if( $hasSlave ): ?>
        dd_master.change(function(){
            let selected = dd_master.find( ":selected" );
            if( selected.attr( 'data-has-duplex' ) === 'false' ) {
                area_slave.show();
                let nextPort = selected.next().val();
                dd_slave.val( nextPort ).trigger('change.select2');
            } else {
                area_slave.hide();
            }
        });
    <?php endif; ?>

    $( document ).ready( function() {
        if( dd_pp.val() !== null ){
            dd_pp.trigger('change');
        }

    });
    /**
     * set all the Patch Panel Panel Port available for the Patch Panel selected
     */
    dd_pp.change( function( ) {
        let fn = "<?= $hasSlave ? "free-duplex-port" : "free-port"  ?>"

        let url     = "<?= url( '/api/v4/patch-panel' )?>/" +  $( this ).val() + "/" + fn;
        let datas   = {
            pppid: <?= $t->ppp->id ?>
        };

        dd_master.html( `<option value="">Loading please wait</option>` ).trigger( 'change.select2' );
        <?php if( $hasSlave ): ?>
            dd_slave.html( `<option value="">Loading please wait</option>` ).trigger( 'change.select2' );
        <?php endif; ?>

        $.ajax( url , {
            data: datas,
            type: 'POST'
        })
        .done( function( data ) {
            let options = `<option value="">Choose a switch port</option>`;
            <?php if( $hasSlave ): ?>
                let optionsSlave = `<option value="">Choose a switch port</option>`;
            <?php endif; ?>
            $.each( data.ports, function( key, value ){
                options += `<option data-has-duplex="${value.isDuplex}" value="${value.id}">${value.name}</option>`;

                if( value.isDuplex === false ){
                    optionsSlave += `<option data-has-duplex="${value.isDuplex}" value="${value.id}">${value.name}</option>`;
                }

            });
            dd_master.html( options );
            <?php if( $hasSlave ): ?>
                dd_slave.html( optionsSlave );
            <?php endif; ?>
        })
        .fail( function() {
            alert( `Error running ajax query for ${url}` );
            throw new Error( `Error running ajax query for ${url}`  );
        })
        .always( function() {
            dd_master.trigger('change.select2');
            <?php if( $hasSlave ): ?>
                dd_slave.trigger('change.select2');
            <?php endif; ?>
        });
    });

</script>