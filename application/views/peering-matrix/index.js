
sessions = {$jsessions};
custs = {$jcusts};

$( 'document' ).ready( function(){

	//alert( custs['112']['shortname'] );
	
	$( "#table-pm" ).delegate( 'td', 'mouseover mouseout', function( event ) {
		
		if( this.id.indexOf( 'td-asn-' ) == 0 ) return; 
		if( this.id.indexOf( 'td-name-' ) == 0 ) return; 
		
		if( event.type == 'mouseover' ) 
		{
		      $(this).parent().addClass( "hover" );
		      $( "colgroup" ).eq( $(this ).index() ).addClass("hover");
	    }
	    else 
	    {
		      $(this).parent().removeClass("hover");
		      $("colgroup").eq($(this).index()).removeClass("hover");
	    }		
	});
	
	
	$( '[id|="btn-zoom"]' ).on( "click", function( event ){
		var i, zoom = 0;
		for( i = 1; i <= 5; i++ )
		{
			if( $( '#td-0-0' ).hasClass( 'zoom' + i  ) )
			{
				zoom = i;
				break;
			}
		}

		if( zoom != 0 )
		{
			var nzoom = ( this.id == 'btn-zoom-out' ) ? zoom - 1 : zoom + 1;
			if( nzoom > 5 ) nzoom = 5;
			if( nzoom < 1 ) nzoom = 1;
			
			$( '.zoom' + zoom ).removeClass( 'zoom' + zoom ).addClass( 'zoom' + nzoom );
		}		
	});
	
});

