<script>

    $( '#delete' ).on(  'click', function( event ) {
        event.preventDefault();

        let network = $( "#network" ).val();

        let html = `<form id="delete-ips" method="POST" action="<?= route( 'ip-address@delete-by-network', [ 'vlanid' => $t->vlan->getId() ] ) ?>">
                                <div>Do you really want to delete this IP Adresses?</div>
                                <input type="hidden"   name="doDelete" value="1">
                                <input type="hidden"   name="network"  value="${network}">
                                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            </form>`;

        bootbox.dialog({
            title: "Delete IP addresses",
            message: html,
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
                        $('#delete-ips').submit();
                    }
                },
            }
        });
    });

    $(document).ready( function() {
        $( '#table-ip'   ).dataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive : true,
            ordering: false,
            paging:   false,
            "autoWidth": false,
            "pageLength": 50
        } );
    });

</script>