<script>
    $( '#delete' ).click( function( e ) {
        e.preventDefault();
        let network = $( "#network" ).val();

        let html = `<form id="delete-ips" method="POST" action="<?= route( 'ip-address@delete-by-network', [ 'vlan' => $t->vlan->id ] ) ?>">
                        <div>Do you really want to delete this IP address?</div>
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
</script>