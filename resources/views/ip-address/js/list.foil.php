<script>

    const protocol = "<?= $t->protocol ?>";

    $(document).ready( function() {
        $( '#ip-address-list' ).show();

        $( '#ip-address-list' ).dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive : true,
            "autoWidth": false,
            pageLength: 50,
            columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: -1 }
        ],
        });
    });

    $( "#vlan" ).select2({ placeholder: "Select a VLAN..." }).on( 'change', function(e) {
        let vlan = this.value;
        window.location = "<?= url( 'ip-address/list' ) ?>/"+ protocol + '/' + vlan;
    });

    $( "a[id|='delete-ip']" ).on( 'click', function( e ) {
        e.preventDefault();
        let ipid = ( this.id ).substring( 10 );
        let vlanid  = $( "#vlan" ).val();
        let html = `<form id="form-delete" method="POST" action="<?= route("ip-address@delete" ) ?>">
                                <div>Do you really want to delete this IP address?</div>

                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="${ipid}">
                                <input type="hidden" name="protocol" value="${protocol}">
                                <input type="hidden" name="vlanid" value="${vlanid}">
                            </form>`;

        bootbox.dialog({
            message: html,
            title: "Delete IP Address",
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $('.bootbox.modal').modal('hide');
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger',
                    callback: function () {
                        $('#form-delete').submit();
                    }
                },
            }
        });
    });
</script>
