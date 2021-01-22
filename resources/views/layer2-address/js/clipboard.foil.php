<script>
    /**
     * initialisation of the Clipboard even on the class in parameter
     */
    let clipboard = new ClipboardJS( '.btn-copy' );

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:
    const mac_input       = $( ".mac-input" );
    const case_btn        = $( "#notes-modal-btn-case" );

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
    $( '.btn-view-l2a' ).click( function( e ) {
        e.preventDefault();
        let mac = $( this ).attr( 'data-object-mac');

        $( "#mac"      ).val( mac );
        $( "#macComma" ).val( formatMac( mac, ':', 2 ) );
        $( "#macDot"   ).val( formatMac( mac, '.', 4 ) );
        $( "#macDash"  ).val( formatMac( mac, '-', 2 ) );
        $( '#notes-modal' ).modal( 'show' );
    });

    /**
     * on click, change the case of the mac addresses (uppercase/lowercase)
     */
    $( '#notes-modal-btn-case' ).on( 'click' ,function( e ) {
        e.preventDefault();
        if( mac_input.hasClass( 'upperCase' ) ) {
            mac_input.removeClass( 'upperCase' ).addClass( 'lowerCase' );
            case_btn.html( '<i class="fa fa-text-height"></i> Uppercase' );
        }  else {
            mac_input.removeClass( 'lowerCase' ).addClass( 'upperCase' );
            case_btn.html( '<i class="fa fa-text-height"></i> Lowercase' );
        }
    })
</script>
