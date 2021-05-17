<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store / <?= $t->file ? 'Edit' : 'Create' ?> File
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm ml-auto" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/docstore/">
            Documentation
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <?php if( !$t->file ): ?>

        <div class="tw-my-8 tw-p-4 tw-border-2 tw-border-gray-500 tw-rounded-lg tw-bg-gray-200">

            <p>
                For convenience, <b>IXP Manager</b> allows superusers to create either <code>.txt</code> or <code>.md</code> (markdown)
                files within the document store.
            </p>
            <p>
                Text files (ending with <code>.txt</code>) will be displayed in a HTML preformatted area
                (<code>&lt;pre&gt;...&lt;/pre&gt;</code>). Markdown files will be parsed and displayed as HTML.
            </p>
            <p>
                You must name your file below such that it ends with either the <code>.txt</code> or <code>.md</code> extension.
            </p>
        </div>
    <?php endif; ?>


    <div class="card-body">

        <?= Former::open_for_files()->method( $t->file ? 'put' : 'post' )
            ->action( $t->file ? route ( 'docstore-file@update', [ 'file' => $t->file ] ) : route ( 'docstore-file@store' ) )
            ->actionButtonsCustomClass( "grey-box")
            ->class('col-8')
            ->rules([
                'name' => 'required|max:100'
            ])
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->autofocus()
            ->blockHelp( "The name of the file (this is as it appears on listings in the web interface rather than on the filesystem). "
                . "<b>This is also the name the downloaded file will have. As such - be sure to keep / set an appropriate filename extension.</b>");
        ?>

        <?= Former::select( 'docstore_directory_id' )
            ->label( 'Directory' )
            ->fromQuery( $t->dirs, 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The directory in which to store the file." );
        ?>

        <?= Former::select( 'min_privs' )
            ->label( 'Minimum privilege' )
            ->fromQuery( \IXP\Models\User::$PRIVILEGES_TEXT_ALL , 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The minimum privilege a user is required to have to view and download the file." );
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

        <div class="form-group">
            <div class="col-lg-offset-2 col-sm-offset-2">
                <div class="card mt-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#body2">Content</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-preview-note nav-link" href="#preview2">Preview</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-body">
                        <div role="tabpanel" class="tab-pane show active" id="body2">
                            <?= Former::textarea( 'fileContent' )
                                ->id( 'fileContent' )
                                ->label( '' )
                                ->rows( 10 )
                                ->blockHelp( "This field supports markdown." )
                            ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview2">
                            <p><em>Preview only applies to markdown files.</em></p>
                            <div class="bg-light p-4 well-preview">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= Former::actions(
            Former::primary_submit( $t->file ? 'Save' : 'Create' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( redirect()->back()->getTargetUrl() )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>

    </div>

<?php $this->append() ?>
