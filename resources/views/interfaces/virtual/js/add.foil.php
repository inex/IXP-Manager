<script>
    $(document).ready( function() {

        <?php
            // FIXME: This if/elseif/else clause adds the appropriate 'back' button. We should not need to rely in JS for this :-(
        ?>
        <?php if( $t->cb ): ?>
            $( "#btn-group div" ).append('<a style="margin-left: 5px;" href="<?= route( 'core-bundle/edit' , [ 'id' => $t->cb->getId() ] ) ?>" class="btn btn-default">Return to Core Bundle</a>');
        <?php elseif( $t->vi ): ?>
            $( "#btn-group div" ).append('<a style="margin-left: 5px;" href="<?= url( 'customer/overview/tab/ports/id').'/'.$t->vi->getCustomer()->getId() ?>" class="btn btn-default">Return to Customer Overview</a>');
        <?php else: ?>
            $( "#btn-group div" ).append('<a style="margin-left: 5px;" href="<?= action( 'Interfaces\VirtualInterfaceController@list' ) ?>" class="btn btn-default">Cancel</a>');
        <?php endif;?>

        if ( $( '#name' ).val() != '' || $( '#description' ).val() != '' || $( '#channel-group' ).val() != '' || $( '#mtu' ).val() != '' ) {
            $( "#advanced-options" ).prop('checked', true);
            $( "#advanced-area" ).show();
        }
    });

    /**
     * hide the help block at loading
     */
    $('p.help-block').hide();

    /**
     * display / hide help sections on click on the help button
     */
    $( "#help-btn" ).click( function() {
        $( "p.help-block" ).toggle();
    });

    /**
     * display or hide the fastlapc area
     */
    $( '#lag_framing' ).change( function(){
        if( this.checked ){
            $( "#fastlacp-area" ).slideDown();
        } else {
            $( "#fastlacp-area" ).slideUp();
        }
    });

    /**
     * display or hide the advanced area
     */
    $( '#advanced-options' ).click( function() {
        $( "#advanced-area" ).slideToggle();
    });

    <?php if( $t->vi ): ?>

    /**
     * on click even allow to delete a Sflow receiver
     */
    $("a[id|='delete-pi']").on('click', function(e){
        e.preventDefault();
        var piid = (this.id).substring(10);
        deletePopup( piid, <?= $t->vi->getId() ?> , 'pi' );
    });

    /**
     * on click even allow to delete a Sflow receiver
     */
    $("a[id|='delete-vli']").on( 'click', function(e) {
        e.preventDefault();
        var vliid = (this.id).substring(11);
        deletePopup( vliid, <?= $t->vi->getId() ?>, 'vli' );
    });

    /**
     * on click even allow to delete a Sflow receiver
     */
    $( "a[id|='delete-sflr']" ).on( 'click', function(e) {
        e.preventDefault();
        var sflrid = (this.id).substring(12);
        deletePopup( sflrid, <?= $t->vi->getId() ?>, 'sflr' );
    });


    <?php endif;?>
</script>
