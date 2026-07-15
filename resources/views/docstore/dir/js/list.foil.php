<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $( '.btn-delete' ).click( function( e ) {
            e.preventDefault();
            let url = this.href;
            let type = $(this).attr( 'data-object-type') === 'file' ? 'file' : 'directory';

            let html = `<form id="form-delete" method="POST" action="${url}">
                                <div>Do you really want to delete this ${type}?`;

            if( type === 'directory' ) {
                html += ' <b>All subdirectories and all files within those directories will also be deleted!</b>';
            }

            html += `</div>
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                    </form>`;

            bootbox.dialog({
                message: html,
                title: `Delete ${type}`,
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

        $( '.btn-infos' ).click( function( e ) {
            e.preventDefault();
            let url = this.href;

            bootbox.dialog({
                message: '<div><p class="text-center"><i class="fa fa-spinner fa-spin text-5xl"></i></p></div>',
                size: "extra-large",
                title: "File Metadata",
                onEscape: true,
                buttons: {
                    cancel: {
                        label: 'Close',
                        callback: function () {
                            $('.bootbox.modal').modal('hide');
                            return false;
                        }
                    }
                }
            });

            $.ajax(url)
                .done(function (data) {
                    // Clone template for metadata, populate with API response
                    const template = $('#file-metadata-template');
                    const fragment = $(document.importNode(template[0].content, true));
                    fragment.find('#meta-filename').text(data.file_name);
                    if (data.created_by != null) {
                        fragment.find('#meta-created-by').text(data.created_by.username + " (" + data.created_by.name + ")");
                    } else {
                        fragment.find('#meta-created-by').html(
                            $('em').text('User no longer exists in database.')
                        );
                    }
                    fragment.find('#meta-dspath').text(data.dspath);
                    fragment.find('#meta-created-at').text(data.created_at);
                    fragment.find('#meta-last-modified').text(data.last_modified);
                    fragment.find('#meta-size').text(data.size);
                    $('.bootbox-body').html( fragment ).scrollTop(0);
                })
                .fail(function () {
                    alert(`Error running ajax query for ${url}`);
                    throw `Error running ajax query for ${url}`;
                })
        });

    });
</script>