
{if isset( $validCustomers )}
    $( "#cust-assign-dialog-close" ).on( 'click', function(){
        dialog.modal( "hide" );
    });
    
    $( "#cust-assign-dialog-assign" ).on( 'click', function(){ 
        window.location.href = '{genUrl controller="ixp" action="assign-customer" id=$ixp->getId()}/cid/' + $( '#customer' ).val();
    });
    
    $( document ).ready( function(){
        $( "#assign-customer-btn" ).on( 'click', function( event ){
            event.preventDefault();
            dialog = $( '#cust-assign-dialog' ).modal( {
                backdrop: true,
                keyboard: true,
                show: true
            });
        } );
    });
{/if}
	