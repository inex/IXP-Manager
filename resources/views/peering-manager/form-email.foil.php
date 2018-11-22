<?= Former::open()->method( 'POST' )
    ->action( "#" )
    ->customWidthClass( 'col-sm-8' )
    ->id( "form-peering-request" );
?>

    <?php if( $t->form == "email" ): ?>
        <?= Former::text( 'to' )
            ->label( 'To' );

        ?>

        <?= Former::text( 'cc' )
            ->label( 'CC' );
        ?>

        <?= Former::text( 'bcc' )
            ->label( 'BCC' );
        ?>

        <?= Former::text( 'subject' )
            ->label( 'Subject' );
        ?>

        <div class="form-group">

            <label for="notes" class="control-label col-lg-2 col-sm-4">Message</label>
            <div class="col-sm-8">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a class="tab-link-body-note" href="#body">Markdown</a>
                    </li>
                    <li role="presentation">
                        <a class="tab-link-preview-note" href="#preview">Preview</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="body">
                        <textarea class="form-control readonlyChange" style="font-family:monospace;" rows="20" id="message" name="message"><?= $t->insert( 'peering-manager/peering-message', [ "peer" => $t->peer, "pp" => $t->pp, "user" => Auth::getUser() ] ); ?></textarea>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="preview">
                        <div class="well well-preview" style="background: rgb(255,255,255);">
                            Loading...
                        </div>
                    </div>
                </div>

                <br><br>
            </div>

        </div>

        <?= Former::hidden( 'input-marksent' )
            ->id( 'input-marksent' )
            ->value( 0 );
        ?>

        <?= Former::hidden( 'input-sendtome' )
            ->id( 'input-sendtome' )
            ->value( 0 );
        ?>

    <?php else: ?>

        <div class="form-group">

            <label for="notes" class="control-label col-lg-2 col-sm-4">Notes</label>
            <div class="col-sm-8">

                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a class="tab-link-body-note" href="#body">Notes</a>
                    </li>
                    <li role="presentation">
                        <a class="tab-link-preview-note" href="#preview">Preview</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="body">
                        <textarea class="form-control" style="font-family:monospace;" rows="20" id="peering-manager-notes" name="peering-manager-notes"><?= $t->peeringManager->getNotes() ?></textarea>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="preview">
                        <div class="well well-preview" style="background: rgb(255,255,255);">
                            Loading...
                        </div>
                    </div>
                </div>

                <br><br>
            </div>

        </div>

    <?php endif; ?>

    <?= Former::hidden( 'peerid' )
        ->id( 'peerid' )
        ->value( $t->peer->getId() );
    ?>

<?= Former::close() ?>




<script>

    /**
     * Adds a prefix when a user goes to add/edit notes (typically name and date).
     */
    function setNotesTextArea() {
        if( $(this).val() == '' ) {
            $(this).val( notesIntro );
        } else {
            $(this).val( notesIntro  + $(this).val() );
        }
        $(this).setCursorPosition( notesIntro.length );
    }

    /**
     * Removes the prefix added by setNotesTextArea() if the notes where not edited
     */
    function unsetNotesTextArea() {
        $(this).val( $(this).val().substring( notesIntro.length ) );
    }

    $( 'document' ).ready( function(){

        // The logic of these two blocks is:
        // 1. if the user clicks on a notes field, add a prefix (name and date typically)
        // 2. if they make a change, remove all the handlers including that which removes the prefix
        // 3. if they haven't made a change, we still have blur / focusout handlers and so remove the prefix
        $( '#peering-manager-notes' ).on( 'focusout', unsetNotesTextArea )
            .on( 'focus', setNotesTextArea )
            .on( 'keyup change', function() { $(this).off('blur focus focusout keyup change') } );


        $('.tab-link-preview-note').on( 'click', function(e) {
            const well_div = $(this).closest('div').find( ".well-preview" );
            e.preventDefault();

            $(this).tab('show');

            $.ajax( MARKDOWN_URL, {
                data: {
                    text: $(this).closest('div').find( "textarea" ).val()
                },
                type: 'POST'
            })
                .done( function( data ) {
                    well_div.html( data.html );
                })
                .fail( function() {
                    well_div.html('Error!');
                });
        });

        $('.tab-link-body-note').on( 'click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });


    });

</script>