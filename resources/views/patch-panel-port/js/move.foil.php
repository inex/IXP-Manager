<script>
    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const dd_pp         = $( "#pp" );
    const dd_master     = $( "#master-port" );
    const dd_slave      = $( "#slave-port" );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    dd_pp.change( () => { setPPP(); } );

    <?php if( $t->ppp->hasSlavePort() ): ?>
    dd_master.change(function(){
        let nextPort = parseInt( dd_master.val()) + parseInt(1);
        if( $( '#slave-port option[value="'+nextPort+'"]' ).length ) {
            dd_slave.val( nextPort );
            dd_slave.trigger('change.select2');
        }
    });
    <?php endif; ?>

    //////////////////////////////////////////////////////////////////////////////////////
    // functions:

    /**
     * set all the Patch Panel Panel Port available for the Patch Panel selected
     */
    function setPPP(){
        let ppId    = dd_pp.val();
        let url     = "<?= url( '/api/v4/patch-panel' )?>/" + ppId + "/patch-panel-port-free";
        let datas   = {pppId: <?= $t->ppp->getId() ?> };


        $( "#master-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger('change.select2');
        <?php if( $t->ppp->hasSlavePort() ): ?>
        $( "#slave-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger('change.select2');
        <?php endif; ?>

        $.ajax( url , {
            data: datas,
            type: 'POST'
        })
            .done( function( data ) {
                let options = `<option value="">Choose a switch port</option>`;
                $.each( data.listPorts, function( key, value ){
                    options += `<option value="${key}">${value}</option>`;
                });
                dd_master.html( options );
                <?php if( $t->ppp->hasSlavePort() ): ?>
                dd_slave.html( options );
                <?php endif; ?>
            })
            .fail( function() {
                throw new Error( `Error running ajax query for ${url}`  );
                alert( `Error running ajax query for ${url}` );
            })
            .always( function() {
                dd_master.trigger('change.select2');
                <?php if( $t->ppp->hasSlavePort() ): ?>
                dd_slave.trigger('change.select2');
                <?php endif; ?>
            });
    }
</script>