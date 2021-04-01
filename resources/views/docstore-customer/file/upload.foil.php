<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <a href="<?= route( 'customer@overview', [ 'cust' => $t->cust->id ] ) ?>">
        <?= $t->cust->name ?>
    </a> ::
    Document Store :: <?= $t->file ? 'Edit' : 'Upload' ?> <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> File
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <div class="card-body">
        <?= Former::open_for_files()->method( $t->file ? 'put' : 'post' )
            ->action( $t->file ? route ( 'docstore-c-file@update', [ 'cust' => $t->cust, 'file' => $t->file ] ) : route ( 'docstore-c-file@store', [ 'cust' => $t->cust ] ) )
            ->actionButtonsCustomClass( "grey-box")
            ->class('col-8')
            ->rules([
                'name' => 'required|max:100'
            ])
        ?>

        <?= Former::text( 'name' )
            ->id('name')
            ->label( 'Name' )
            ->blockHelp( "The name of the file (this is as it appears on listings in the web interface rather than on the filesystem). "
                . "<b>This is also the name the downloaded file will have - so use the appropriate extension.</b>");
        ?>

        <?= Former::text( 'sha256' )
            ->id('sha256')
            ->label( 'SHA256' )
            ->disabled( $t->file ? 1 : false )
            ->blockHelp( "SHA checksums can be used to verify the authenticity / integrity of downloaded files. The primary use-case in development "
                . "was for official documents - please see the documentation for more information. If you enter a SHA256 checksum, it will be "
                . "verified on upload. If you leave it blank, the SHA256 checksum will be calculated by IXP Manager.");
        ?>

        <?= Former::select( 'docstore_customer_directory_id' )
            ->label( 'Directory' )
            ->fromQuery( $t->dirs, 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The directory in which to store the file." );
        ?>

        <?= Former::select( 'min_privs' )
            ->label( 'Minimum privilege' )
            ->fromQuery( \IXP\Models\User::$PRIVILEGES_TEXT , 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The minimum privilege a user is required to have to view and download the file." );
        ?>

        <?= Former::file( 'uploadedFile' )
            ->id( 'uploadedFile' )
            ->label( ( $t->file ? 'Replace' : 'Upload' ) . ' File' )
            ->class( 'form-control border-0 shadow-none' )
            ->multiple( false )
            ->blockHelp( $t->file ? "You only need to choose a file here if you wish to replace the existing one. Do not select a file to edit other details but leave the current file in place."
                : "Select the file you wish to upload." );
        ?>

        <div class="form-group">
            <div class="col-lg-offset-2 col-sm-offset-2">
                <div class="card mt-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#body">Description</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-body">
                        <div role="tabpanel" class="tab-pane show active" id="body">
                            <?= Former::textarea( 'description' )
                                ->id( 'description' )
                                ->label( '' )
                                ->rows( 2 )
                                ->blockHelp( "If provided, this text will appear in a tooltip above the filename when the mouse is hovered over it. "
                                    . "<b>For best user experience, we would recommend providing a descriptive filename and avoid using this field.</b> "
                                    . "If you must use it, try and keep it short. This field supports markdown." )
                            ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview">
                            <div class="bg-light p-4 well-preview">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= Former::actions(
            Former::primary_submit( $t->file ? 'Save' : 'Upload' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( redirect()->back()->getTargetUrl() )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
        <?php if( $t->file ): ?>
            $( document ).ready( function() {
                $('#uploadedFile').on( 'input', function( e ) {
                    $('#sha256').removeAttr('disabled').val('');
                });
            });
        <?php endif; ?>

        $( document ).ready( function() {
            $("#uploadedFile").on('input', function() {
                if( $( "#name" ).val() === '' ) {
                    $( "#name" ).val( this.files[0].name );
                }
            });
        });
    </script>
<?php $this->append() ?>