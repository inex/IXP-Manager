<script>

    /**
     * Set the form action depending on the button clicked
     */
    $( ".atlas-run-action" ).on( 'click', function( event ) {
        event.preventDefault();

        let url         = this.href;
        let action_name = $(this).attr( 'title');
        let method      = $(this).attr( 'data-method');

        let html = `<form id="action" method="POST" action="${url}">
                        <div>Do you really want to ${action_name}?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="${method}" />
                    </form>`;

        bootbox.dialog({
            message: html,
            title: action_name,
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
                    label: 'Submit',
                    className: 'btn-primary',
                    callback: function () {
                        $('#action').submit();
                    }
                },
            }
        });
    });
</script>