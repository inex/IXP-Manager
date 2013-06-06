$( 'document' ).ready( function() {
    
    $( "a[id|='poll-select']"  ).on( 'click', function( event ){
        event.preventDefault();
        var type = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
        
        $( "#poll-actions input[type=checkbox]" ).each( function( idx ) {
            if( type == "inverse" )
            {
                if( $( this ).prop( "checked" ) )
                    $( this ).removeAttr( "checked" ).trigger( "change" );    
                else
                    $( this ).attr( "checked", "checked" ).trigger( "change" );
            }
            else if( type == "all" )
                $( this ).attr( "checked", "checked" ).trigger( "change" );
            else if( type == "none" )
                $( this ).removeAttr( "checked" ).trigger( "change" );
        });
    });
    
    $( "#shared-type" ).on( 'change', function( event ){
        $( "#poll-group-type" ).trigger( 'click' );
    });
    
    $( "a[id|='poll-group']"  ).on( 'click', function( event ){
        event.preventDefault();
        var type = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
        
        $( "#poll-action" ).val( type );
        $( "#poll-actions" ).trigger( "submit" );
        
    });
    
    $( "tr[id|='poll-tr']"  ).on( 'click', function( event ){
        
        if( $( event.target ).prop("tagName") != "TD" )
            return;
        
        var id = $( this ).attr( 'id' ).substr( $( this ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
        
        if( $( "#switch-port-" + id ).prop( "checked" ) )
            $( "#switch-port-" + id ).removeAttr( "checked" ).trigger( "change" );    
        else
            $( "#switch-port-" + id ).attr( "checked", "checked" ).trigger( "change" );
        
    });
    
    $( "input[id|='switch-port']"  ).on( 'change', function( event ){
        var id = $( this ).attr( 'id' ).substr( $( this ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
        
        if( $( this ).prop( "checked" ) )   
            $( "#poll-tr-" + id ).css( "background-color", "#F0F0F0" );
        else
            $( "#poll-tr-" + id ).css( "background-color", "#FFFFFF" );
    });
    
    
    $( "select[id|='port-type']"  ).on( 'change', function( event ){
        
        var id = $( event.target ).attr( 'id' ).substr( $( event.target ).attr( 'id' ).lastIndexOf( '-' ) + 1 );
        var throb =  ossThrobber( 20, 10, 1 );
        
        $( '#port-type-state-' + id ).html( "" );
        throb.appendTo( $( '#port-type-state-' + id ).get(0) ).start()
        
        var success = false;
        $.ajax({
            url: "{genUrl controller='switch-port' action='ajax-set-type'}/id/" + id,
            data: { type: $( this ).val() },
            async: true,
            cache: false,
            type: 'POST',
            timeout: 10000,
            success: function( data ){
                if( data == "ok" ) {
                    success = true;
                } else {
                    ossAddMessage( data, 'error' );
                }
            },
            error: ossAjaxErrorHandler,
            complete: function(){
                if( success ) {
                    $( '#port-type-state-' + id ).html( '<i class="icon-ok"></i>' );
                } else {
                    $( '#port-type-state-' + id ).html( '<i class="icon-remove"></i>' );
                }
            }
        });
     
    });
});
