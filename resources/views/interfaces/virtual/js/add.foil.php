<script>
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// we'll need these handles to html elements in a few places:
    ///
    const btn_advanced   = $( '#advanced-options' );
    const btn_delete     = $( '#delete-vi-<?= $t->vi ? $t->vi->id : "xxxxxxx" ?>' );
    const cb_lag_framing = $( '#lag_framing' );
    const div_advanced   = $( "#advanced-area" );
    const div_fastlacp   = $( "#fastlacp-area" );

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// action bindings:
    ///

    // display or hide the advanced area
    btn_advanced.click( function(e){
        e.preventDefault();
        div_advanced.slideToggle();
        btn_delete.slideToggle({
        easing: "easeOutQuad",
        start: function() {
            jQuery(this).css('display','inline-flex');
        }});
    });

    // display or hide the fastlapc area
    cb_lag_framing.change( () => { cb_lag_framing.is(":checked") ? div_fastlacp.slideDown() : div_fastlacp.slideUp() } );

    cb_lag_framing.trigger( 'change' );

    <?php if( $t->vi ): ?>
        /**
         * on click even allow to delete a Sflow receiver
         */
        $( '.btn-delete-vi'  ).click( function( e ){
            e.preventDefault();
            deletePopup( $( this ), 'vi');
        });

        /**
         * on click even allow to delete a Sflow receiver
         */
        $( '.btn-delete-pi' ).click( function( e ){
            e.preventDefault();
            deletePopup( $( this ), 'pi');
        });

        /**
         * on click even allow to delete a VLI
         */
        $( '.btn-delete-vli' ).click( function( e ) {
            e.preventDefault();
            deletePopup( $( this ), 'vli');
        });

        /**
         * on click even allow to delete a Sflow receiver
         */
        $( '.btn-duplicate-vli' ).click( function(e) {
            e.preventDefault();
            duplicateVliPopup( $( this ).attr( 'data-object-id' ), <?= $t->vi->id ?> );
        });

        /**
         * on click even allow to delete a Sflow receiver
         */
        $( ".btn-delete-sflr" ).click( function(e) {
            e.preventDefault();
            deletePopup( $( this ), 'sflr');
        });
    <?php endif; ?>

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Initial states
    ///
    if ( $( '#name' ).val() !== '' || $( '#description' ).val() !== '' || $( '#channelgroup' ).val() !== '' || $( '#mtu' ).val() !== '' ) {
        div_advanced.show();
        btn_delete.css( "display", "inline-block" );
    } else {
        btn_delete.hide();
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * function to show the duplicate vli popup
     */
    function duplicateVliPopup( id, viid ) {

        let html = `
                <p>
                Duplicating a VLAN interface allows you to copy IP addresses and other settings to a new VLAN
                interface on another VLAN. Typical use cases for this is creating a quarantine VLAN interface
                on an 802.1q tagged port for example.
                </p>
                <p>
                Duplicating can also be used to copy all values to a new VLAN without needing to remember / create the
                matching IP addresses. In this case, copy and then delete the older VLAN interface.
                </p>
                <p>
                    <b>
                    While IXP Manager will let you create multiple VLAN interfaces on an untagged port, it is
                    only a convenience for the above but may have unexpected consequences in production.
                    </b>
                </p>
                <p>
                Please select the VLAN: <select id="duplicateTo" class="chzn-select">
                    <option></option>
                <?php foreach( $t->vlans as $id => $vl): ?>
                    <option value="<?= $vl->id ?>"><?= $vl->name ?></option>
                <?php endforeach; ?>
                </select>
                </p>
            `;


        dialog = bootbox.dialog({
            message: html,
            title: "Duplicate the VLAN Interface",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Close',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        return false;
                    }
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Duplicate',
                    callback: function () {
                        let duplicateTo = $( "#duplicateTo" );
                        if( duplicateTo.val() ) {
                            window.location.href = "<?= url( 'interfaces/vlan/duplicate' ) ?>/"+ id + "/to/" + duplicateTo.val();
                        }
                    }
                }
            }
        });

        dialog.init( function(){
            $("#duplicateTo").select2({
                width: "50%", // need to override the changed default
                placeholder: "Select a VLAN..."
            });
        });
    }
</script>