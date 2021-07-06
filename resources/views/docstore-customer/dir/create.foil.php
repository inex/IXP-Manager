<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Document Store / <?= $t->dir ? 'Edit' : 'Create' ?> Directory
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
    <div class="card-body">

        <?= Former::open()->method( $t->dir ? 'put' : 'post' )
            ->id( 'form' )
            ->action( $t->dir ? route ( 'docstore-c-dir@update', [ 'cust' => $t->cust, 'dir' => $t->dir->id ] ) : route ( 'docstore-c-dir@store', [ 'cust' => $t->cust ] ) )
            ->actionButtonsCustomClass( "grey-box")
            ->class('col-8')
            ->rules([
                'name' => 'required|max:100'
            ])
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->autofocus()
            ->blockHelp( "The name of the directory (this is as it appears on listings in the web interface rather than on the filesystem)." );
        ?>

        <?= Former::select( 'parent_dir_id' )
            ->label( 'Parent Directory' )
            ->fromQuery( $t->dirs, 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "Where to create the new directory." );
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
                                ->blockHelp( "If you enter content here, it will appear at the top of the "
                                    . "directory listing in a well element (bordered and emphasised). This is useful to explain the content of "
                                    . "a directory.<br><br>This field supports markdown." )
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

        <?= Former::hidden( 'cust_id' )
            ->value( $t->cust->id );
        ?>

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

        <?php if( $t->dir ): ?>
            $( document ).ready(function() {
                $( "#parent_dir_id option[value=" + <?= $t->dir->id ?> +"]" ).attr( 'disabled','disabled' );
            });
        <?php endif; ?>

    </script>
<?php $this->append() ?>