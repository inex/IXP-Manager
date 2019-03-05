<script>
    /**
     * initialisation of the Clipboard even on the class in parameter
     */
    let clipboard = new ClipboardJS( '.btn-copy' );

    /**
     * initialisation of tooltip
     */
    $( '.btn-copy' ).tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    /**
     * display a tooltip on the Clipboard button
     */
    function setTooltip( btn, message ) {
        $( btn ).attr( 'data-original-title', message )
            .tooltip( 'show' );
    }

    /**
     * hide a tooltip on the Clipboard button
     */
    function hideTooltip( btn ) {
        setTimeout( function() {
            $( btn ).tooltip( 'hide' );
        }, 1000);
    }

    /**
     * success even when using Clipboard
     */
    clipboard.on( 'success', function( e ) {
        setTooltip( e.trigger, 'Copied!' );
        hideTooltip( e.trigger );
    });


    /**
     * function formatting the mac address with a delimiter and the number of characters after which each delimiter will appear
     */
    function formatMac( mac , delimiter, nbCar ){
        return mac.match( new RegExp( '.{1,'+ nbCar +'}', 'g' ) ).join( delimiter );
    }

    /**
     * on click, show a popup of the mac address with different formats
     */
    $(document).on( 'click', "a[id|='view-l2a']", function(e) {
        e.preventDefault();
        var mac = this.name;
        $( "#mac"      ).val( mac );
        $( "#macComma" ).val( formatMac( mac, ':', 2 ) );
        $( "#macDot"   ).val( formatMac( mac, '.', 4 ) );
        $( "#macDash"  ).val( formatMac( mac, '-', 2 ) );

        $( '#notes-modal' ).modal( 'show' );
    });

    /**
     * on click, change the case of the mac addresses (uppercase/lowercase)
     */
    $( document ).on( 'click', "#notes-modal-btn-case" ,function( e ){
        e.preventDefault();
        if($( ".mac-input" ).hasClass( 'upperCase' ) ){
            $( ".mac-input" ).removeClass( 'upperCase' ).addClass( 'lowerCase' );
            $( "#notes-modal-btn-case" ).html( '<i class="fa fa-text-height"></i> Uppercase' );
        }  else {
            $( ".mac-input" ).removeClass( 'lowerCase' ).addClass( 'upperCase' );
            $( "#notes-modal-btn-case" ).html( '<i class="fa fa-text-height"></i> Lowercase' );
        }
    });
</script>
