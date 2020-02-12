<?php
    /** @var Foil\Template\Template $t */
    /** @var $t->active */

    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Document Store :: <?= $t->dir ? 'Edit' : 'Create' ?> Directory
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>

<?php $this->append() ?>

<?php $this->section('content') ?>


    <div class="card-body">

        <?= Former::open()->method( $t->dir ? 'put' : 'post' )
            ->id( 'form' )
            ->action( $t->dir ? route ( 'docstore-dir@update', [ 'dir' => $t->dir->id ] ) : route ( 'docstore-dir@store' ) )
            ->actionButtonsCustomClass( "grey-box")
            ->class('col-8')
            ->rules([
                'name' => 'required|max:100'
            ])
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of the directory (this is as it appears on listings in the web interface rather than on the filesystem)." );
        ?>

        <?= Former::select( 'parent_dir' )
            ->label( 'Root Directory' )
            ->fromQuery( $t->dirs, 'name' )
            ->placeholder( 'Choose a root directory' )
            ->addClass( 'chzn-select' );
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
                                ->rows( 5 )
                                ->blockHelp( "This field supports markdown" )
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
                Former::primary_submit( $t->dir ? 'Save' : 'Create' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( redirect()->back()->getTargetUrl() )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            );
        ?>

        <?= Former::close() ?>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <script>
    </script>
<?php $this->append() ?>