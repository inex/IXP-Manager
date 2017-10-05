<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action ($t->controller.'@listAction') ?>">
        <?=  $t->data[ 'feParams' ]->pagetitle  ?>
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Edit <?= $t->data[ 'feParams' ]->pagetitle  ?> </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action ($t->controller.'@listAction') ?>">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<?= $t->alerts() ?>
    <div class="well col-sm-12">
        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( action ( $t->controller.'@storeAction' ) )
            ->customWidthClass( 'col-sm-3' )
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'sname' )
            ->label( 'Short Name' )
            ->blockHelp( "" );
        ?>

        <?= Former::checkbox( 'primary' )
            ->label( 'Primary Infrastructure' )
            ->checked_value( 1 )
            ->unchecked_value( 0 )
            ->blockHelp( "" );
        ?>

        <?= Former::select( 'ixf_ix_id' )
            ->id( 'ixf_ix_id' )
            ->label( 'IX-F DB IX ID' )
            ->placeholder( 'Choose an option' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::select( 'pdb-ixp' )
            ->label( 'Peering DB IX ID' )
            ->placeholder( 'Choose an option' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( action ($t->controller.'@listAction') ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->params[ 'inf'] ? $t->params[ 'inf']->getId() : '' )
        ?>

        <?= Former::close() ?>

    </div>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>
<script>
    let dd_ixp = $( '#ixf_ix_id' );
    let dd_pdb = $( '#pdb-ixp' );

    $(document).ready(function() {

        $.ajax( "<?= url('api/v4/ix-f/ixp') ?>" )
            .done( function( data ) {
                let selectedixp, selectNow;


                let options = `<option value=''>Choose an option</option>\n`;

                <?php if( $t->params[ 'inf' ] ):?>
                    selectedixp = <?= $t->params[ 'inf' ]->getIxfIxId() ?>
                <?php else: ?>
                    selectedixp = false;
                <?php endif; ?>

                $.each( data, function ( i, ixp ) {
                    selectNow = '';
                    if( selectedixp == ixp.ixf_id ){
                        selectNow = 'selected';
                    }
                    options += "<option " +selectNow+ " value=\"" + ixp.ixf_id + "\">" + ixp.name + "</option>\n"
                });
                dd_ixp.html( options );
            })
            .fail( function() {
                throw new Error("Error running ajax query for patch-panel-port/$id");
            })
            .always( function() {
                dd_ixp.trigger( "changed" );
            });


        $.ajax( "<?= url('api/v4/peeringdb/ix') ?>" )
            .done( function( data ) {
                let selectedpdb, selectNow;


                let options = `<option value=''>Choose an option</option>\n`;

                <?php if( $t->params[ 'inf' ] ):?>
                    selectedpdb = <?= $t->params[ 'inf' ]->getPeeringdbIxId() ?>
                <?php else: ?>
                    selectedpdb = false;
                <?php endif; ?>

                $.each( data, function ( i, ixp ) {
                    selectNow = '';
                    if( selectedpdb == ixp.pdb_id ){
                        selectNow = 'selected';
                    }
                    options += "<option " +selectNow+ " value=\"" + ixp.pdb_id + "\">" + ixp.name + "</option>\n"
                });
                dd_pdb.html( options );
            })
            .fail( function() {
                throw new Error("Error running ajax query for patch-panel-port/$id");
            })
            .always( function() {
                dd_pdb.trigger( "changed" );
            });
    });

</script>
<?php $this->append() ?>