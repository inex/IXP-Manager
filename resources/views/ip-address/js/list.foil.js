<script>

    const protocol = "<?= $t->protocol ?>";

    $(document).ready( function() {
        $( '#ip-address-list' ).dataTable( { "autoWidth": false, pageLength: 50 } ).show();
    });

    $( "#vlan" ).select2({ placeholder: "Select a VLAN..." }).on( 'change', function(e) {
        let vlan = this.value;
        window.location = "<?= url( 'ip-address/list' ) ?>/"+ protocol + '/' + vlan;
    });

    $( "a[id|='delete-ip']" ).on( 'click', function( e ) {
        e.preventDefault();
        let ipid = ( this.id ).substring( 10 );

        bootbox.confirm({
            message: "Do you really want to delete this IP address?",
            buttons: {
                confirm: {
                    label: 'Confirm',
                    className: 'btn-primary',
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-default',
                }
            },
            callback: function ( result ) {
                if( result) {
                    $.ajax( "<?= url('ip-address/delete/' )?>/" + protocol + "/" + ipid,{
                        type : 'POST'
                    })
                    .done( function( data ) {
                        location.reload();
                    })
                    .fail( function(){
                        throw new Error( `Error running ajax query while delete IPv${protocol} address with database ID ${ipid}.` );
                    })
                }
            }
        });
    });
</script>
