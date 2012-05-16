
<div class="modal hide" id="modal-confirm">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body">
        <p>
            Deletion is <strong>a permanent action</strong> and it cannot be undone.
        </p>
        <p>
            Are you sure you want to delete this object?
        </p>
    </div>
    <div class="modal-footer">
        <a data-dismiss="modal" class="btn btn-success">Cancel</a>
        <a id="modal-confirm-action" href="{genUrl}" class="btn btn-danger">Delete</a>
    </div>
</div>


<script type="text/javascript">

$(document).ready(function() {

	$( "#modal-confirm" ).modal({
		'show': false
	});

	$('a[id|="object-delete"]').click( function( event ){

		var id = substr( $( this ).attr( 'id' ), 14 );

		if( $( this ).attr( 'data-url' ) ) {
		    $( '#modal-confirm-action' ).attr( 'href', $( this ).attr( 'data-url' ) );
		} else {
		    $( '#modal-confirm-action' ).attr( 'href', "{genUrl controller=$controller action="delete"}/id/" + id );
	    }
		
		$( "#modal-confirm" ).modal( { 'show': true } );
	});

});

</script>
